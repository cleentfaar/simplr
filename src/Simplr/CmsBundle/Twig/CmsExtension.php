<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\CmsBundle\Twig;

use Simplr\Core\Entity\Media;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CmsExtension extends \Twig_Extension
{

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'twig_extensions.simplr.cms';
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
    public function getMediaUrl($media, array $options = array())
    {
        if (is_int($media)) {
            $media = $this->container->get('simplr.mediamanager')->getMedia($media);
        } elseif (!is_object($media) || !($media instanceof Media)) {
            throw new \Exception("Given media argument must be either a media ID or Media entity instance");
        }
        return $this->container->get('simplr.mediaurlgenerator')->generateUrl($media, $options);
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
