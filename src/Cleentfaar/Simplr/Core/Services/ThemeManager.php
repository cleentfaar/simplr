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

class ThemeManager
{

    /**
     * @var string|null
     */
    private $activeTheme;

    /**
     * @var array|null
     */
    private $activeThemeOptions;

    /**
     * @var array
     */
    private $activeThemeConfig = array();

    /**
     * @var bool
     */
    private $activeThemeFetched = false;

    /**
     * @var string
     */
    private $pathToThemes;

    /**
     * @var OptionManager
     */
    private $optionManager;

    /**
     * @param string $pathToThemes
     * @param OptionManager $optionManager
     */
    public function __construct($pathToThemes, OptionManager $optionManager)
    {
        if (!is_dir($pathToThemes)) {
            throw new \Exception(sprintf("Path provided for themes is not a valid directory (%s)", $pathToThemes));
        }
        $this->pathToThemes = $pathToThemes;
        $this->optionManager = $optionManager;
    }

    public function registerActiveTheme(EventDispatcherInterface $dispatcher)
    {
        $activeTheme = $this->getActiveTheme();
        if ($activeTheme !== null) {
            $config = $this->getActiveThemeConfig();
            if (isset($config['hooks'])) {
                foreach ($config['hooks'] as $hook => $callable) {
                    $dispatcher->addListener($hook, $callable);
                }
            }
        }
    }

    /**
     * @param $option
     * @return null|mixed
     */
    public function getActiveThemeOption($option, $default = null)
    {
        $options = $this->getActiveThemeOptions();
        if (isset($options->{$option})) {
            return $options->{$option};
        }
        return $default;
    }

    /**
     * @return null|string
     */
    public function getActiveThemePath()
    {
        $activeTheme = $this->getActiveTheme();
        if ($activeTheme !== null) {
            return $this->getPathToTheme($activeTheme);
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getActiveThemeViewsPath()
    {
        $activeTheme = $this->getActiveTheme();
        if ($activeTheme !== null) {
            $path = $this->getPathToTheme($activeTheme);
            return $path . '/Resources/views';
        }
        return null;
    }

    /**
     * @return array|null
     */
    public function getActiveThemeOptions()
    {
        $activeTheme = $this->getActiveTheme();
        if ($activeTheme === null) {
            return null;
        }
        if (!isset($this->activeThemeOptions)) {
            $this->activeThemeOptions = array();
        }
        return $this->activeThemeOptions;
    }

    /**
     * @return array
     */
    public function getActiveThemeConfig()
    {
        $activeTheme = $this->getActiveTheme();
        if ($activeTheme !== null) {
            return $this->activeThemeConfig;
        }
        return array();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getActiveTheme()
    {
        if ($this->activeThemeFetched === false) {
            $activeThemeDb = $this->optionManager->getOptionValue('active_theme', null);
            if ($activeThemeDb !== null) {
                $configuration = $this->findConfiguration($activeThemeDb);
                if (empty($configuration)) {
                    throw new \Exception(sprintf(
                        "No matching configuration could be found in the filesystem ".
                        "for the active theme in the database (%s)",
                        $activeThemeDb
                    ));
                }
                $this->activeTheme = $activeThemeDb;
                $this->activeThemeConfig = $configuration;
                $this->activeThemeOptions = $this->optionManager->getOptionValue(
                    'theme_options_'.$activeThemeDb,
                    new \stdClass()
                );
            } else {
                throw new \Exception("No active theme was defined, this should never happen!");
            }
            $this->activeThemeFetched = true;
        }
        return $this->activeTheme;
    }

    /**
     * @param $name
     * @return array
     */
    public function findConfiguration($name)
    {
        $attempt = $this->pathToThemes . '/' . $name . '/theme.php';
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

    /**
     * @param $theme
     * @return mixed
     */
    public function getPathToTheme($theme)
    {
        return realpath($this->pathToThemes . '/' . $theme);
    }
}
