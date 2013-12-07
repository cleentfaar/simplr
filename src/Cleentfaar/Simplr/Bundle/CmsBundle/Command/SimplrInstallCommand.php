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
        'doctrine:database:create' => array(),
        'doctrine:schema:update' => array(),
        'doctrine:fixtures:load' => array(),
        'simplr:assets:install' => array(),
        'assetic:dump' => array(),
    );
    protected $forcedCommandsInDev = array(
        'doctrine:schema:update'
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
        foreach ($this->preCommands as $commandNamespace => $arguments) {
            try {
                if ($input->getOption('no-interaction')) {
                    $arguments['--no-interaction'] = true;
                }
                if ($input->getOption('quiet')) {
                    $arguments['--quiet'] = true;
                }
                $arguments['--env'] = $input->getOption('env') ? $input->getOption('env') : 'prod';
                $arguments['--process-isolation'] = true;
                if ($arguments['--env'] != 'prod' && in_array($commandNamespace, $this->forcedCommandsInDev)) {
                    $arguments['--force'] = true;
                }
                $command = $this->getApplication()->find($commandNamespace);
                $commandInput = new ArrayInput($arguments);

                $returnCode = $command->run($commandInput, $output);

                $output->writeln(sprintf('Successfully executed %s', $commandNamespace));
                $failed = false;
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
            return true;
        } else {
            $output->writeln("<error>Simplr failed to be installed!</error>");
            if (!empty($failedReasons)) {
                $output->writeln("<error>Reasons:</error>");
                foreach ($failedReasons as $subject => $reasons) {
                    $output->writeln(sprintf("\t<error>%s:</error>", $subject));
                    foreach ($reasons as $reason) {
                        $output->writeln(sprintf("\t\t<error>- %s</error>", $reason));
                    }
                }
            }
            return false;
        }
    }
}
