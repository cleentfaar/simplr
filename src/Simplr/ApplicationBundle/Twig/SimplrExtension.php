<?php
/**
 * This file is part of the Clevr CMS
 *
 * @author Cas Leentfaar
 * @see http://github.com/cleentfaar/clevr
 */
namespace Simplr\Twig;

use Simplr\ApplicationBundle\Model\Media;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SimplrExtension extends \Twig_Extension
{

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simplr_twig_extension';
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
        $attr = array(
            'src' => $media->getUrl($options),
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
        return $this->app['simplr_mediamanager']->getMedia($mediaId);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getMediaUrl($mediaId, array $options = array())
    {
        $media = $this->getMedia($mediaId);
        if ($media !== null) {
            $media->getUrl($options);
        }
        return null;
    }


    /**
     * @param $name
     * @return null|string
     */
    public function getOptionValue($name, $default = null)
    {
        return $this->app['simplr_optionmanager']->getOptionValue($name, $default);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getThemeOptionValue($name, $default = null)
    {
        return $this->app['simplr_thememanager']->getActiveThemeOption($name, $default);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function widget($name)
    {
        return $this->app['simplr_widgetmanager']->renderWidget($name);
    }

    /**
     * @param $name
     * @return null|string
     */
    public function widgetContainer($name)
    {
        return $this->app['simplr_widgetmanager']->renderWidgetContainer($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function routeExists($name)
    {
        $routes = $this->app['routes'];
        return (null === $routes->get($name)) ? false : true;
    }
}
