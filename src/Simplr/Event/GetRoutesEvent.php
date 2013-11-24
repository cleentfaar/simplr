<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Event;

use Silex\Route;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class GetRoutesEvent
 * @package Cleentfaar\SimplrBundle\Event
 */
class GetRoutesEvent extends Event
{

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $collection;

    /**
     * @var \Symfony\Component\Config\Loader\LoaderResolver
     */
    private $resolver;

    public function __construct()
    {
        $this->collection = new RouteCollection();
        $locator = new FileLocator();
        $this->resolver = new LoaderResolver(array(
            new YamlFileLoader($locator),
            new XmlFileLoader($locator),
            new PhpFileLoader($locator),
        ));
    }

    public function import($resource, $type = null)
    {
        $resource = realpath($resource);
        if (!$resource) {
            return null;
        }
        $loader = $this->resolver->resolve($resource, $type);
        $collection = $loader->load($resource, $type);
        $this->getCollection()->addCollection($collection);
    }

    public function add($name, Route $route)
    {
        $this->getCollection()->add($name, $route);
    }

    public function getCollection()
    {
        return $this->collection;
    }
}