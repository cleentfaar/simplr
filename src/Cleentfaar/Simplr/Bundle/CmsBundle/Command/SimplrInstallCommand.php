<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Bundle\CmsBundle\Command;

use Cleentfaar\Simplr\Core\Simplr;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SimplrInstallCommand extends ContainerAwareCommand
{

    const CURRENT_DIRECTORY = 'current directory';

    protected $preCommands = array(
        'test' => array(
            'doctrine:database:drop' => array('--force' => true),
            'doctrine:database:create' => array(),
            'doctrine:schema:create' => array(),
            'doctrine:fixtures:load' => array(),
            'simplr:assets:install' => array(),
            'assetic:dump' => array(),
        ),
        'dev' => array(
            'doctrine:database:drop' => array('--force' => true),
            'doctrine:database:create' => array(),
            'doctrine:schema:create' => array(),
            'doctrine:fixtures:load' => array(),
            'simplr:assets:install' => array(),
            'assetic:dump' => array(),
        ),
        'prod' => array(
            'doctrine:database:create' => array(),
            'doctrine:schema:update' => array(),
            'doctrine:fixtures:load' => array(),
            'simplr:assets:install' => array(),
            'assetic:dump' => array(),
        ),
    );

    /**
     * @var string
     */
    protected $targetPath;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('simplr:install')
            ->setDefinition(
                array(
                    new InputArgument(
                        'target',
                        InputArgument::OPTIONAL,
                        'Optional target directory',
                        self::CURRENT_DIRECTORY
                    ),
                    new InputOption(
                        'ignore-lock',
                        null,
                        InputOption::VALUE_NONE,
                        'Used during development to repeat the installation without caring about created data'
                    ),
                )
            )
            ->setDescription('Installs the Simplr CMS')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command installs the Simplr CMS, optionally into a given
directory (defaults to the current working directory).

<info>php %command.full_name% path/to/project</info>

EOT
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetPath = rtrim($input->getArgument('target'), '/');

        if ($targetPath === self::CURRENT_DIRECTORY) {
            $targetPath = getcwd();
        } elseif (!is_dir($targetPath)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The target directory "%s" does not exist.',
                    $targetPath
                )
            );
        }
        $this->targetPath = $targetPath;

        $output->writeln(
            sprintf(
                "Installing the Simplr CMS into <comment>%s</comment>",
                $this->targetPath
            )
        );

        /**
         * @var Simplr $simplr
         */
        $simplr = $this->getContainer()->get('simplr.instance');
        $isInstalled = $simplr->isInstalled() ? true : false;

        if ($isInstalled === true) {
            throw new \Exception(
                "Oops! Simplr seems to be installed already! ".
                "If you are sure it isn't, run <comment>simplr:diagnose</comment> ".
                "to see if there are any problems with your current installation"
            );
        }

        return $this->installSimplr($input, $output);
    }

    /**
     * @todo Find out why we need to use NullOutput to prevent output from subcommands,
     *       even though the --quiet argument is passed}</p>
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function installSimplr(InputInterface $input, OutputInterface $output)
    {
        $failed = true;
        $failedReasons = array();
        $env = $input->getOption('env') ? $input->getOption('env') : 'prod';

        /**
         * @var ProgressHelper $progress
         */
        $totalCommands = count($this->preCommands[$env]);
        $output->writeln(sprintf('Executing <comment>%s</comment> commands to complete installation', $totalCommands));
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($output, $totalCommands);
        $progress->display();
        foreach ($this->preCommands[$env] as $commandNamespace => $commandArguments) {
            /**
             * @var ProgressHelper $commandProgress
             */
            try {
                $command = $this->getApplication()->find($commandNamespace);
                $commandArguments['--quiet'] = true;
                $commandArguments['--env'] = $env;
                $inputArray = array_merge(array('command' => $commandNamespace), $commandArguments);
                $commandInput = new ArrayInput($inputArray);
                $commandInput->setInteractive($input->isInteractive());
                $nullOutput = new NullOutput();
                $progress->clear();
                $output->write(sprintf(" Executing <comment>%s</comment>           ", $commandNamespace));
                $progress->display();
                $command->run($commandInput, $nullOutput);

                $failed = false;

                if (substr($commandNamespace, 0, 9) == 'doctrine:') {
                    $connection = $this->getContainer()->get('doctrine')->getConnection();
                    if ($connection->isConnected()) {
                        $connection->close();
                    }
                }
                $progress->advance();
            } catch (\Exception $e) {
                $failed = true;
                $failedReasons['Command '.$commandNamespace][] = $e->getMessage();
                $output->writeln(sprintf('Failed to execute %s', $commandNamespace));
                break;
            }

        }
        $progress->clear();
        $output->write("                                ");
        $progress->display();
        $progress->clear();
        $progress->finish();
        return $this->handleResult($failed, $failedReasons, $input, $output);
    }

    /**
     * @param bool $failed
     * @param array $failedReasons
     * @param OutputInterface $output
     * @return bool
     */
    protected function handleResult($failed, array $failedReasons, InputInterface $input, OutputInterface $output)
    {
        if ($failed === false) {
            if ($input->getOption('ignore-lock') !== true) {
                $lockPath = $this->getContainer()->get('simplr.instance')->getInstallationLockPath();
                if ($lockPath !== null) {
                    $filesystem = new Filesystem();
                    try {
                        $filesystem->remove($lockPath);
                    } catch (\Exception $e) {
                        $output->writeln(
                            sprintf(
                                "<error>Failed to remove lock-file, you will need to remove it manually ".
                                "before continuing (<comment>%s</comment>)</error>",
                                $lockPath
                            )
                        );
                    }
                }
            }
            $output->writeln(
                sprintf(
                    "Congratulations! Simplr was installed successfully into <comment>%s</comment>",
                    $this->targetPath
                )
            );
            return 0;
        } else {
            $output->writeln("<error>Simplr failed to be installed!</error>");
            if (!empty($failedReasons)) {
                $output->writeln("<error>Reasons:</error>");
                foreach ($failedReasons as $subject => $reasons) {
                    $output->writeln(sprintf("\t%s:", $subject));
                    foreach ($reasons as $reason) {
                        $output->writeln(sprintf("\t\t- %s", $reason));
                    }
                }
            }
            return 1;
        }
    }
}
