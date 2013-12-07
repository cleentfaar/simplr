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

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class PageManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $activePages = array();

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->activePages = $this->fetchActivePages();
    }

    /**
     * @return RouteCollection
     */
    public function getActivePageRoutes()
    {
        $routeCollection = new RouteCollection();
        foreach ($this->getActivePages() as $entity) {
            $defaults = $entity->getDefaults();
            $options = $entity->getOptions();
            $requirements = $entity->getRequirements();
            if (!array_key_exists('_controller', $defaults)) {
                $defaults['_controller'] = 'pages.controller:indexAction';
                $defaults['page'] = $entity;
            } else {
                $defaults['_controller'] = $entity->getController();
            }

            $route = new Route($entity->getSlug(), $defaults, $requirements, $options);
            $routeCollection->add('route_'.$entity->getId(), $route);
        }
        return $routeCollection;
    }

    /**
     * @return array
     */
    public function getActivePages()
    {
        return $this->activePages;
    }

    /**
     * @return array
     */
    private function fetchActivePages()
    {
        $pages = $this->em->getRepository('Simplr\Entity\Page')->findBy(array('active' => true));
        return $pages;
    }
}
