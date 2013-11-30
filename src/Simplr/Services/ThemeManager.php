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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class ThemeManager
 * @package Simplr\Services
 */
class ThemeManager
{
    /**
     * @var OptionManager
     */
    private $optionManager;

    /**
     * @var array
     */
    private $themeOptions = array();

    /**
     * @var array
     */
    private $themeConfigs = array();

    /**
     * @var array
     */
    private $availableThemes;

    /**
     * @var string
     */
    private $activeTheme;

    /**
     * @var array
     */
    private $activeThemeConfig = array();

    /**
     * @param OptionManager $optionManager
     */
    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
        $activeTheme = $this->fetchActiveTheme();
        if ($activeTheme !== null) {
            $this->activeThemeFetched = $activeTheme;
            $themeConfig = $this->getThemeConfig($activeTheme);
            if (!empty($themeConfig)) {
                $this->activeTheme = $activeTheme;
                $this->activeThemeConfig = $themeConfig;
            }
        }
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function registerListeners(EventDispatcherInterface $dispatcher)
    {
        $activeThemeConfig = $this->getActiveThemeConfig();
        if (!empty($activeThemeConfig)) {
            foreach ($activeThemeConfig['hooks'] as $eventName => $callable) {
                $dispatcher->addListener($eventName, $callable);
            }
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function getActiveThemeOption($name)
    {
        $activeTheme = $this->getActiveTheme();
        if ($activeTheme === null) {
            return null;
        }
        $options = $this->getThemeOptions($activeTheme);
        if (isset($options->{$name})) {
            return $options->{$name};
        }
        return null;
    }

    /**
     * @param $theme
     * @return array|null
     */
    public function getThemeConfig($theme)
    {
        if (!array_key_exists($theme, $this->themeConfigs)) {
            $this->themeConfigs[$theme] = $this->findThemeConfig($theme);
        }
        return $this->themeConfigs[$theme];
    }

    /**
     * @param $theme
     * @return mixed
     */
    public function getThemeOptions($theme)
    {
        if (!isset($this->themeOptions[$theme])) {
            $this->themeOptions[$theme] = $this->fetchThemeOptions($theme);
        }
        return $this->themeOptions[$theme];
    }

    public function getPathToTheme($theme)
    {
        return SIMPLR_PATHTO_THEMES . '/' . $theme;
    }

    /**
     * @param null $restrictedName
     * @return array
     */
    private function findThemeConfig($theme)
    {
        $path = $this->getPathToTheme($theme);
        $finder = new Finder();
        $files = $finder->files()->in($path)->name('theme.php')->depth(0);
        $config = array();
        foreach ($files as $file) {
            $path = $file->getRealpath();
            $configIncluded = include $path;
            $valid = $this->validateConfig($config);
            if ($valid === true) {
                $config = $configIncluded;
            }
            break;
        }
        return $config;
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
     * @param $name
     * @return mixed
     */
    public function themeExists($name)
    {
        $themesDir = $this->getPathToTheme($name);
        $filesystem = new Filesystem();
        return $filesystem->exists($themesDir);
    }

    /**
     * @return null|string
     */
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    /**
     * @return null|array
     */
    public function getActiveThemeConfig()
    {
        return $this->activeThemeConfig;
    }

    /**
     * @return array
     */
    public function getAvailableThemes()
    {
        if (!isset($this->availableThemes)) {
            $this->availableThemes = $this->findAvailableThemes();
        }
        return $this->availableThemes;
    }

    /**
     * @param $theme
     * @return array
     */
    private function fetchThemeOptions($theme)
    {
        $options = $this->optionManager->getOptionValue('theme_options_'.$theme, array());
        return $options;
    }

    /**
     * @return string|null
     */
    private function fetchActiveTheme()
    {
        $activeTheme = $this->optionManager->getOptionValue('active_theme', null);
        return $activeTheme;
    }
}