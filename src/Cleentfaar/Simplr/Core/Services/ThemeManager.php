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

use Cleentfaar\Simplr\Core\BaseTheme;
use Cleentfaar\Simplr\Core\Pluggable;
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
     * @var BaseTheme|null
     */
    private $activeThemeObject;

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
            $this->getActiveThemeObject()->register($dispatcher);
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
     * @return null|BaseTheme
     */
    public function getActiveThemeObject()
    {
        $activeTheme = $this->getActiveTheme();
        if ($activeTheme !== null) {
            return $this->activeThemeObject;
        }
        return null;
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
                $activeTheme = $this->getTheme($activeThemeDb);
                if ($activeTheme === null) {
                    throw new \Exception(sprintf(
                        "No matching object could be found in the filesystem for the active theme in the database (%s)",
                        $activeThemeDb
                    ));
                }
                $activeThemeConfig = array_merge(
                    $this->getDefaultThemeConfiguration(),
                    $activeTheme->getConfiguration()
                );
                $this->activeTheme = $activeThemeDb;
                $this->activeThemeObject = $activeTheme;
                $this->activeThemeConfig = $activeThemeConfig;
                $this->activeThemeOptions = $this->optionManager->getOptionValue('theme_options_'.$activeThemeDb, new \stdClass());
            } else {
                throw new \Exception("No active theme was defined, this should never happen!");
            }
            $this->activeThemeFetched = true;
        }
        return $this->activeTheme;
    }

    /**
     * @param $name
     * @return Pluggable|null
     */
    public function getTheme($name)
    {
        $namespace = "Simplr\\Themes\\$name\\Theme";
        $classPath = $this->pathToThemes . "/" . $name . "/Theme.php";
        if (!file_exists($classPath)) {
            return null;
        }
        require_once $classPath;
        if (class_exists($namespace)) {
            return new $namespace;
        }
        return null;
    }

    /**
     * @param $theme
     * @return mixed
     */
    public function getPathToTheme($theme)
    {
        return realpath($this->pathToThemes . '/' . $theme);
    }

    /**
     * @return array
     */
    private function getDefaultThemeConfiguration()
    {
        return array(
            'events' => array(),
        );
    }
}
