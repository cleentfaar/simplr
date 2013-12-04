<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\PluggableBundle\Twig;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PluggableExtension extends \Twig_Extension
{

    /**
     * @param FilesystemLoader $loader
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, FilesystemLoader $loader)
    {
        $this->container = $container;
        $this->loader = $loader;
        $themePath = $container->get('simplr.thememanager')->getActiveThemeViewsPath();
        if ($themePath !== null) {
            $viewNamespace = "current_theme";
            $this->loader->addPath($themePath, $viewNamespace);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'twig_extensions.simplr.pluggable';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('theme_option', array($this, 'getThemeOptionValue')),
        );
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getThemeOptionValue($name, $default = null)
    {
        return $this->container->get('simplr.thememanager')->getActiveThemeOption($name, $default);
    }
}
