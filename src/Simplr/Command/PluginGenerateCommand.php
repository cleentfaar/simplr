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
use Simplr\Exception\FilesystemException;

/**
 * PluginGenerateCommand generates a new plugin skeleton from a given set of parameters
 * Can be used to develop a custom plugin for further sharing
 *
 * @author Cas Leentfaar <info@casleentfaar.com>
 */
class PluginGenerateCommand extends Command
{
    private $pluginName;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('plugin:generate')
            ->setDefinition(array(
                new InputArgument('plugin_name', InputArgument::REQUIRED, 'The plugin name'),
            ))
            ->setDescription('Generates a new plugin skeleton from a given set of parameters')
            ->setHelp(<<<EOF
The <info>%command.name%</info> generates a new plugin skeleton from a given set of parameters
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pluginName = $input->getArgument('plugin_name');
        $app = $this->getSilexApplication();
        $pluginTemplateDir = $app['simplr.skeletons_path'] . DIRECTORY_SEPARATOR . 'plugin';
        $pluginTargetDir = $app['simplr.plugins_path'] . DIRECTORY_SEPARATOR . $this->pluginName;
        $pluginTitle = str_replace('_', ' ', $this->pluginName);
        $pluginTitle = str_replace('-', ' ', $pluginTitle);
        $pluginTitle = ucwords($pluginTitle);
        if (!$app['filesystem']->exists($pluginTemplateDir)) {
            throw new FilesystemException(sprintf("The plugin skeleton path could not be found in %s", $pluginTemplateDir));
        }
        if ($app['simplr_pluginmanager']->pluginExists($this->pluginName)) {
            throw new FilesystemException(sprintf("A plugin with the same name (%s) already exists, uninstall it first", $this->pluginName));
        }
        $output->writeln(sprintf("Creating plugin with name '%s' from directory '%s'", $this->pluginName, $pluginTemplateDir));
        $app['filesystem']->mirror($pluginTemplateDir, $pluginTargetDir);
        $app['filesystem']->rename($pluginTargetDir . DIRECTORY_SEPARATOR . 'plugin_name.php', $pluginTargetDir . DIRECTORY_SEPARATOR . $this->pluginName . '.php');

        $files = $app['finder']->files()->in($pluginTargetDir);
        foreach ($files as $file) {
            $content = $file->getContents();
            $content = str_replace('%plugin_title%', $pluginTitle, $content);
            $content = str_replace('%plugin_name%', $this->pluginName, $content);
            $app['filesystem']->dumpFile($file->getRealpath(), $content);
            $output->writeln(sprintf("Scanned file %s inside new plugin for replacements", $file->getRealpath()));
        }

        $output->writeln(sprintf("Plugin '%s' was created successfully in '%s', you can now go ahead and make further changes.", $this->pluginName, $pluginTargetDir));
        $this->pluginName = null;
    }
}
