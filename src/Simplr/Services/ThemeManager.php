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
            $availableThemes = $this->getAvailableThemes();
            if (isset($availableThemes[$activeTheme])) {
                $this->activeTheme = $activeTheme;
                $this->activeThemeConfig = $availableThemes[$activeTheme];
            }
        }
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function registerListeners(EventDispatcherInterface $dispatcher)
    {
        $activeTheme = $this->getActiveTheme();
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
     * @return mixed
     */
    public function getThemeOptions($theme)
    {
        if (!isset($this->themeOptions[$theme])) {
            $this->themeOptions[$theme] = $this->fetchThemeOptions($theme);
        }
        return $this->themeOptions[$theme];
    }

    /**
     * @param null $restrictedName
     * @return array
     */
    private function findAvailableThemes($restrictedName = null)
    {
        $finder = new Finder();
        $files = $finder->files()->in(SIMPLR_PATHTO_THEMES)->name('theme.php')->depth(1);
        $themes = array();
        foreach ($files as $file) {
            $path = $file->getRealpath();
            $name = pathinfo(pathinfo($path, PATHINFO_DIRNAME), PATHINFO_BASENAME);
            if (!$restrictedName || $name == $restrictedName) {
                $config = include $path;
                $valid = $this->validateConfig($config);
                if ($valid === true) {
                    $themes[$name] = $config;
                }
            }
        }
        return $themes;
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
        $themesDir = SIMPLR_PATHTO_THEMES . '/' . $name;
        return $this->app['filesystem']->exists($themesDir);
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