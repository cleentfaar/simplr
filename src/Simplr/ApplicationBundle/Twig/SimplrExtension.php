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
            new \Twig_SimpleFunction('get_media', array($this, 'getMedia')),
            new \Twig_SimpleFunction('get_media_url', array($this, 'getMediaUrl')),
            new \Twig_SimpleFunction('get_media_path', array($this, 'getMediaPath')),
            new \Twig_SimpleFunction('option', array($this, 'getOptionValue')),
            new \Twig_SimpleFunction('theme_option', array($this, 'getThemeOptionValue')),
            new \Twig_SimpleFunction('widgetcontainer', array($this, 'widgetContainer')),
            new \Twig_SimpleFunction('widget', array($this, 'widget')),
        );
    }

    public function getFilters()
    {
        return array(
            //new \Twig_SimpleFilter('resize', array($this, 'resizeImage')),
        );
    }

    /**
     * @param $name
     * @return null|Media
     */
    public function getMedia($mediaId)
    {
        return $this->container->get('simplr.mediamanager')->getMedia($mediaId);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getMediaPath($media, $options = array())
    {
        if (is_int($media)) {
            $media = $this->getMedia($media);
        } elseif (!is_object($media) || !($media instanceof Media)) {
            throw new \Exception("Must supply either a media ID or media instance");
        }
        if ($media !== null) {
            return $media->getPath();
        }
        return null;
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getMediaUrl($media, array $options = array())
    {
        if (is_int($media)) {
            $media = $this->getMedia($media);
        } elseif (!is_object($media) || !($media instanceof Media)) {
            throw new \Exception("Must supply either a media ID or media instance");
        }
        if ($media !== null) {
            if (array_key_exists('resize', $options)) {
                $path = $this->getMediaPath($media);
                /**
                 * @var \Twig_SimpleFilter $imagineFilter
                 */
                $imagineFilter = $this->container->get('twig')->getFilter('imagine_filter');
                $callable = $imagineFilter->getCallable();

                $resizeFilter = $options['resize'];
                list($width, $height) = $this->container->get('simplr.mediamanager')->getDimensionsForResizeFilter($resizeFilter);
                $absoluteUrl = isset($options['absoluteUrl']) ? (bool) $options['absoluteUrl'] : false;
                $uri = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);
                $uri = $uri . '-' . $width . 'x' . $height . '.' . pathinfo($path, PATHINFO_EXTENSION);
                $uri = $callable($uri, $resizeFilter, $absoluteUrl);

                return $uri;
            }
            return $this->container->get('simplr.mediamanager')->getMediaUrl($media);
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
