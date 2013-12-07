<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Services;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PluginManager
{

    /**
     * @var string
     */
    private $pathToPlugins;

    /**
     * @var array
     */
    private $activePlugins = array();

    /**
     * @var array
     */
    private $activePluginOptions = array();

    /**
     * @var bool
     */
    private $activePluginsFetched = false;

    /**
     * @var OptionManager
     */
    private $optionManager;

    public function __construct($pathToPlugins, OptionManager $optionManager)
    {
        if (!is_dir($pathToPlugins)) {
            throw new \Exception(sprintf("Path provided for plugins is not a valid directory (%s)", $pathToPlugins));
        }
        $this->pathToPlugins = $pathToPlugins;
        $this->optionManager = $optionManager;
    }

    public function getActivePlugins()
    {
        if ($this->activePluginsFetched === false) {
            $activePluginsDb = $this->optionManager->getOptionValue('active_plugins', array());
            if (!empty($activePluginsDb)) {
                foreach ($activePluginsDb as $plugin) {
                    $configuration = $this->findConfiguration($plugin);
                    if (empty($configuration)) {
                        throw new \Exception(sprintf(
                            "No matching configuration could be found in the filesystem ".
                            "for the active plugin in the database (%s)",
                            $plugin
                        ));
                    }
                    $this->activePlugins[$plugin] = $configuration;
                    $this->activePluginOptions[$plugin] = $this->optionManager->getOptionValue(
                        'plugin_options_'.$plugin,
                        new \stdClass()
                    );
                }
            }
            $this->activePluginsFetched = true;
        }
        return $this->activePlugins;
    }

    public function registerActivePlugins(EventDispatcherInterface $dispatcher)
    {
        foreach ($this->getActivePlugins() as $configuration) {
            if (isset($configuration['hooks'])) {
                foreach ($configuration['hooks'] as $hook => $callable) {
                    $dispatcher->addListener($hook, $callable);
                }
            }
        }
    }

    /**
     * @param $name
     * @return array
     */
    public function findConfiguration($name)
    {
        $attempt = $this->pathToPlugins . '/' . $name . '/plugin.php';
        if (!file_exists($attempt)) {
            return array();
        }
        ob_start();
        $configuration = include($attempt);
        ob_end_clean();
        if (!is_array($configuration)) {
            return array();
        }
        return $configuration;
    }
}
