<?php
/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ThemeGenerateCommand generates a new theme skeleton from a given set of parameters
 * Can be used to develop a custom theme for further sharing
 *
 * @author Cas Leentfaar <info@casleentfaar.com>
 */
class ThemeGenerateCommand extends Command
{
    private $themeName;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('theme:generate')
            ->setDefinition(array(
                new InputArgument('theme_name', InputArgument::REQUIRED, 'The theme name'),
            ))
            ->setDescription('Generates a new theme skeleton from a given set of parameters')
            ->setHelp(<<<EOF
The <info>%command.name%</info> generates a new theme skeleton from a given set of parameters
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->themeName = $input->getArgument('theme_name');
        $app = $this->getSilexApplication();
        $themeTemplateDir = $app['simplr.skeletons_path'] . DIRECTORY_SEPARATOR . 'theme';
        $themeTargetDir = $app['simplr.themes_path'] . DIRECTORY_SEPARATOR . $this->themeName;
        $themeTitle = str_replace('_', ' ', $this->themeName);
        $themeTitle = str_replace('-', ' ', $themeTitle);
        $themeTitle = ucwords($themeTitle);
        if (!$app['filesystem']->exists($themeTemplateDir)) {
            throw new \Exception(sprintf("The theme skeleton path could not be found in %s", $themeTemplateDir));
        }
        if ($app['simplr_thememanager']->themeExists($this->themeName)) {
            throw new \Exception(sprintf("A theme with the same name (%s) already exists, uninstall it first", $this->themeName));
        }
        $output->writeln(sprintf("Creating theme with name '%s' from directory '%s'", $this->themeName, $themeTemplateDir));
        $app['filesystem']->mirror($themeTemplateDir, $themeTargetDir);
        $app['filesystem']->rename($themeTargetDir . DIRECTORY_SEPARATOR . 'theme_name.php', $themeTargetDir . DIRECTORY_SEPARATOR . $this->themeName . '.php');

        $files = $app['finder']->files()->in($themeTargetDir);
        foreach ($files as $file) {
            $content = $file->getContents();
            $content = str_replace('%theme_title%', $themeTitle, $content);
            $content = str_replace('%theme_name%', $this->themeName, $content);
            $app['filesystem']->dumpFile($file->getRealpath(), $content);
            $output->writeln(sprintf("Scanned file %s inside new theme for replacements", $file->getRealpath()));
        }

        $output->writeln(sprintf("Theme '%s' was created successfully in '%s', you can now go ahead and make further changes.", $this->themeName, $themeTargetDir));
        $this->themeName = null;
    }
}
