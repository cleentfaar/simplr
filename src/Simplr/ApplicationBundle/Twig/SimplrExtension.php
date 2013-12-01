<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Twig;

use Simplr\ApplicationBundle\Entity\Media;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SimplrExtension extends \Twig_Extension
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
        /*foreach ($container->get('simplrs.pluginmanager')->getActivePluginPaths() as $path) {
            $pluginDir = basename($path);
            $viewPath = realpath($path . '/Resources/views');
            $viewNamespace = "".$pluginDir."Plugin";
            $this->loader->addPath($viewPath, $viewNamespace);
        }*/
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simplr.twig_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('routeExists', array($this, 'routeExists')),
            new \Twig_SimpleFunction('image', array($this, 'getImageElementFromMedia'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('media', array($this, 'getMedia')),
            new \Twig_SimpleFunction('media_url', array($this, 'getMediaUrl')),
            new \Twig_SimpleFunction('option', array($this, 'getOptionValue')),
            new \Twig_SimpleFunction('theme_option', array($this, 'getThemeOptionValue')),
            new \Twig_SimpleFunction('widgetcontainer', array($this, 'widgetContainer')),
            new \Twig_SimpleFunction('widget', array($this, 'widget')),
        );
    }

    /**
     * @param $mediaId
     * @param array $options
     * @return string
     */
    public function getImageElementFromMedia($mediaId, array $options)
    {
        $media = $this->getMedia($mediaId);
        if ($media === null) {
            return null;
        }
        $url = $this->container->get('simplr.mediamanager')->getMediaUrl($media, $options);
        $attr = array(
            'src' => $url,
            'title' => $media->getTitle(),
            'alt' => $media->getTitle(),
        );
        $attr = array_merge($attr, $options['attributes']);
        if (isset($options['dimensions'])) {
            list($dimensionWidth, $dimensionHeight) = explode("x", $options['dimensions']);
            $attr['width'] = $dimensionWidth;
            $attr['height'] = $dimensionHeight;
        }
        $attrstr = '';
        foreach ($attr as $attKey => $attVal) {
            $attrstr .= $attKey.'="'.$attVal.'" ';
        }
        return '<img '.trim($attrstr).'/>';
    }

    /**
     * @param $name
     * @return null|Media
     */
    public function getMedia($mediaId)
    {
        return new Media();
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getMediaUrl($mediaId, array $options = array())
    {
        $media = $this->getMedia($mediaId);
        if ($media !== null) {
            return $this->container->get('simplr.mediamanager')->getMediaUrl($media, $options);
        }
        return null;
    }


    /**
     * @param $name
     * @return null|string
     */
    public function getOptionValue($name, $default = null)
    {
        return $this->container->get('simplr.optionmanager')->getOptionValue($name, $default);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getThemeOptionValue($name, $default = null)
    {
        return $this->container->get('simplr.thememanager')->getActiveThemeOption($name, $default);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function widget($name)
    {
        return $this->container->get('simplr.widgetmanager')->renderWidget($name);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function widgetContainer($name)
    {
        return $this->container->get('simplr.widgetmanager')->renderWidgetContainer($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function routeExists($name)
    {
        return (null === $this->container->get('router')->getRouteCollection()->get($name)) ? false : true;
    }
}
