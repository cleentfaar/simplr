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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SimplrInstallCommand extends ContainerAwareCommand
{

    const CURRENT_DIRECTORY = 'current directory';

    protected $preCommands = array(
        'test' => array(
            'doctrine:database:create' => array(),
            'doctrine:fixtures:load' => array(),
            'simplr:assets:install' => array(),
            'assetic:dump' => array(),
        ),
        'dev' => array(
            'doctrine:database:drop' => array(),
            'doctrine:database:create' => array(),
            'doctrine:fixtures:load' => array(),
            'simplr:assets:install' => array(),
            'assetic:dump' => array(),
        ),
        'prod' => array(
            'doctrine:database:create' => array(),
            'doctrine:schema:update' => array(),
            'doctrine:fixtures:load' => array('--append'),
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
                    new InputArgument('target', InputArgument::OPTIONAL, 'Optional target directory', self::CURRENT_DIRECTORY),
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function installSimplr(InputInterface $input, OutputInterface $output)
    {
        $failed = true;
        $failedReasons = array();
        $env = $input->getOption('env') ? $input->getOption('env') : 'prod';
        foreach ($this->preCommands[$env] as $commandNamespace => $commandArguments) {
            try {
                $arguments = array();
                $arguments['--env'] = $env;
                $argumentsString = $this->argumentsToString($arguments);
                $command = $this->getApplication()->find($commandNamespace);
                $commandInput = $input;
                foreach ($commandArguments as $k => $v) {
                    $commandInput->setOption($k, $v);
                }

                $returnCode = $command->run($commandInput, $output);

                $output->writeln(sprintf('Successfully executed %s (returned %s), with arguments: %s', $commandNamespace, $returnCode, $argumentsString));
                $failed = false;

                // we have to close the connection after dropping the database so we don't get "No database selected" error
                $connection = $this->getContainer()->get('kernel')->getContainer()->get('doctrine')->getConnection();
                if ($connection->isConnected()) {
                    $connection->close();
                }
            } catch (\Exception $e) {
                $failed = true;
                $failedReasons['Command '.$commandNamespace][] = $e->getMessage();
                $output->writeln(sprintf('Failed to execute %s', $commandNamespace));
                break;
            }
        }
        $output->writeln("");
        return $this->handleResult($failed, $failedReasons, $output);
    }

    private function argumentsToString($arguments)
    {
        $str = '';
        foreach ($arguments as $arg => $val) {
            if (is_int($arg)) {
                $str .= $val.' ';
            } else {
                $str .= $arg.'='.$val.' ';
            }
        }
        return $str;
    }

    /**
     * @param bool $failed
     * @param array $failedReasons
     * @param OutputInterface $output
     * @return bool
     */
    protected function handleResult($failed = false, array $failedReasons, OutputInterface $output) {
        if ($failed === false) {
            $lockPath = $this->getContainer()->get('simplr.instance')->getInstallationLockPath();
            if ($lockPath !== null) {
                $filesystem = new Filesystem();
                try {
                    $filesystem->remove($lockPath);
                } catch (\Exception $e) {
                    $output->writeln(
                        sprintf(
                            "<error>Failed to remove lock-file, you will need to remove it manually before continuing (<comment>%s</comment>)</error>",
                            $lockPath
                        )
                    );
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
