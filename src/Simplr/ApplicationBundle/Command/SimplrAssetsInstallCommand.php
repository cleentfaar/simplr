<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand as BaseAssetsInstallCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class SimplrAssetsInstallCommand extends BaseAssetsInstallCommand
{

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->filesystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('simplr:assets:install')
            ->setDefinition(
                array(
                    new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
                )
            )
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the assets instead of copying it')
            ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
            ->setDescription('Installs web assets under a public web directory')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command installs assets into a given
directory (e.g. the web directory).

<info>php %command.full_name% web</info>

A "assets" directory will be created inside the target directory, and the
"Resources/public" directory of each plugin/theme will be copied into it.

To create a symlink to each plugin/theme instead of physically copying its assets, use the
<info>--symlink</info> option:

<info>php %command.full_name% web --symlink</info>

To make symlink relative, add the <info>--relative</info> option:

<info>php %command.full_name% web --symlink --relative</info>

EOT
            )
        ;
    }

    /**
     * @param $dir
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    private function installAppAssets($dir, InputInterface $input, OutputInterface $output)
    {
        // Create the specific assets directory otherwise symlink will fail.
        $assetsDir = $dir.'/assets/simplr/';
        $this->filesystem->mkdir($assetsDir, 0777);

        if (is_dir($originDir = $this->getContainer()->get('kernel')->getRootDir() . '/Resources/public')) {
            $targetDir  = rtrim($assetsDir, '/');

            $output->writeln(
                sprintf(
                    'Installing assets for <comment>App</comment> into <comment>%s</comment>',
                    $targetDir
                )
            );

            $this->filesystem->remove($targetDir);

            if ($input->getOption('symlink')) {
                if ($input->getOption('relative')) {
                    $relativeOriginDir = $this->filesystem->makePathRelative($originDir, realpath($assetsDir));
                } else {
                    $relativeOriginDir = $originDir;
                }
                $this->filesystem->symlink($relativeOriginDir, $targetDir);
            } else {
                $this->filesystem->mkdir($targetDir, 0777);
                // We use a custom iterator to ignore VCS files
                $this->filesystem->mirror($originDir, $targetDir, Finder::create()->in($originDir));
            }
        } else {
            $output->writeln('Installing assets for <comment>App</comment>... no assets found!');
        }
        return true;
    }

    /**
     * @param $dir
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    private function installThemeAssets($dir, InputInterface $input, OutputInterface $output)
    {
        $this->filesystem = new Filesystem();
        // Create the specific assets directory otherwise symlink will fail.
        $assetsDir = $dir.'/assets/themes/';
        $this->filesystem->mkdir($assetsDir, 0777);
        $theme = $this->getContainer()->get('simplr.thememanager')->getActiveTheme();
        $themeObject = $this->getContainer()->get('simplr.thememanager')->getActiveThemeObject();
        if ($themeObject !== null) {
            $reflection = new \ReflectionClass($themeObject);
            $assetsPath = dirname($reflection->getFileName()) . '/Resources/public';
            if (is_dir($originDir = $assetsPath)) {
                $targetDir  = $assetsDir.strtolower($theme);

                $output->writeln(
                    sprintf(
                        "Installing assets for theme <comment>%s</comment> into <comment>%s</comment>",
                        $theme,
                        $targetDir
                    )
                );

                $this->filesystem->remove($targetDir);

                if ($input->getOption('symlink')) {
                    if ($input->getOption('relative')) {
                        $relativeOriginDir = $this->filesystem->makePathRelative($originDir, realpath($assetsDir));
                    } else {
                        $relativeOriginDir = $originDir;
                    }
                    $this->filesystem->symlink($relativeOriginDir, $targetDir);
                } else {
                    $this->filesystem->mkdir($targetDir, 0777);
                    // We use a custom iterator to ignore VCS files
                    $this->filesystem->mirror($originDir, $targetDir, Finder::create()->in($originDir));
                }
            }
        }
        return true;
    }

    /**
     * @param $dir
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    private function installPluginAssets($dir, InputInterface $input, OutputInterface $output)
    {
        // Create the specific assets directory otherwise symlink will fail.
        $assetsDir = $dir.'/assets/plugins/';
        $this->filesystem->mkdir($assetsDir, 0777);

        $plugins = $this->getContainer()->get('simplr.pluginmanager')->getActivePlugins();
        if (!empty($plugins)) {
            foreach ($plugins as $name => $pluginObject) {
                if (is_dir($originDir = $pluginObject->getAssetsPath())) {
                    $targetDir  = $assetsDir.strtolower($name);

                    $output->writeln(
                        sprintf(
                            "Installing assets for plugin <comment>%s</comment> into <comment>%s</comment>",
                            $name,
                            $targetDir
                        )
                    );

                    $this->filesystem->remove($targetDir);

                    if ($input->getOption('symlink')) {
                        if ($input->getOption('relative')) {
                            $relativeOriginDir = $this->filesystem->makePathRelative($originDir, realpath($assetsDir));
                        } else {
                            $relativeOriginDir = $originDir;
                        }
                        $this->filesystem->symlink($relativeOriginDir, $targetDir);
                    } else {
                        $this->filesystem->mkdir($targetDir, 0777);
                        // We use a custom iterator to ignore VCS files
                        $this->filesystem->mirror($originDir, $targetDir, Finder::create()->in($originDir));
                    }
                }
            }
        } else {
            $output->writeln('Installing assets for plugins... no plugins found!');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When the target directory does not exist or symlink cannot be used
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetArg = rtrim($input->getArgument('target'), '/');

        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The target directory "%s" does not exist.',
                    $input->getArgument('target')
                )
            );
        }

        if (!function_exists('symlink') && $input->getOption('symlink')) {
            throw new \InvalidArgumentException(
                'The symlink() function is not available on your system.'.
                'You need to install the assets without the --symlink option.'
            );
        }

        $output->writeln(
            sprintf(
                "Installing assets using the <comment>%s</comment> option",
                $input->getOption('symlink') ? 'symlink' : 'hard copy'
            )
        );

        $this->installPluginAssets($targetArg, $input, $output);
        $this->installThemeAssets($targetArg, $input, $output);
        $this->installAppAssets($targetArg, $input, $output);

    }
}
