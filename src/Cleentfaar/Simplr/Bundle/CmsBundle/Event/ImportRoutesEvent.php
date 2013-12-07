<?php
namespace Cleentfaar\Simplr\Bundle\CmsBundle\Event;

use Cleentfaar\Simplr\Bundle\CmsBundle\Routing\ClevrCmsLoader;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\RouteCollection;

class ImportRoutesEvent extends Event
{

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $collection;

    /**
     * @var \Cleentfaar\ClevrCmsBundle\Routing\ClevrCmsLoader
     */
    private $loader;

    public function __construct(RouteCollection $collection, ClevrCmsLoader $loader)
    {
        $this->collection = $collection;
        $this->loader = $loader;
    }

    public function import($resource, $type)
    {
        $importedRoutes = $this->loader->import($resource, $type);
        $this->getCollection()->addCollection($importedRoutes);
    }

    public function getLoader() {
        return $this->loader;
    }

    public function getCollection() {
        return $this->collection;
    }
}