<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Services;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class PluginManager
 * @package Simplr\Services
 */
class PluginManager
{
    /**
     * @var OptionManager
     */
    private $optionManager;

    /**
     * @var array|null
     */
    private $activePluginsFetched = array();

    /**
     * @var array
     */
    private $activePlugins = array();

    /**
     * @param OptionManager $optionManager
     */
    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
        $this->activePluginsFetched = $this->fetchActivePlugins();
        if (!empty($this->activePluginsFetched)) {
            $this->activePlugins = $this->findAvailablePlugins($this->activePluginsFetched);
        }
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function registerListeners(EventDispatcherInterface $dispatcher)
    {
        foreach ($this->getActivePlugins() as $plugin => $config) {
            foreach ($config['hooks'] as $eventName => $callable) {
                $dispatcher->addListener($eventName, $callable);
            }
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function pluginExists($name)
    {
        $pluginDir = SIMPLR_PATHTO_PLUGINS . DIRECTORY_SEPARATOR . $name;
        return $this->app['filesystem']->exists($pluginDir);
    }

    /**
     * @return array
     */
    public function getActivePluginsFetched()
    {
        return $this->activePluginsFetched;
    }

    /**
     * @return array
     */
    public function getActivePlugins()
    {
        return $this->activePlugins;
    }

    /**
     * @param array $restrictedNames
     * @return array
     */
    private function findAvailablePlugins($restrictedNames = array())
    {
        $finder = new Finder();
        $pluginFiles = $finder->files()->in(SIMPLR_PATHTO_PLUGINS)->name('plugin.php')->depth(1);
        $plugins = array();
        foreach ($pluginFiles as $file) {
            $path = $file->getRealpath();
            $pluginName = pathinfo(pathinfo($path, PATHINFO_DIRNAME), PATHINFO_BASENAME);
            if (empty($restrictedNames) || in_array($pluginName, $restrictedNames)) {
                $pluginConfig = include $path;
                $valid = $this->validateConfig($pluginConfig);
                if ($valid === true) {
                    $plugins[$pluginName] = $pluginConfig;
                }
            }
        }
        return $plugins;
    }

    /**
     * @param $config
     * @return bool
     */
    private function validateConfig(&$config)
    {
        if (!is_array($config)) {
            return false;
        }
        if (!array_key_exists('version', $config)) {
            return false;
        }
        if (array_key_exists('hooks', $config)) {
            if (!is_array($config['hooks'])) {
                return false;
            }
        } else {
            $config['hooks'] = array();
        }
        return true;
    }

    /**
     * @return array
     */
    private function fetchActivePlugins()
    {
        $activePlugins = $this->optionManager->getOptionValue('active_plugins', array());
        return $activePlugins;
    }
}