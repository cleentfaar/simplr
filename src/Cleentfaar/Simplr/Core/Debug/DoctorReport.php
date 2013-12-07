<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Debug;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctorReport
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \DateTime
     */
    private $dateTimeCreated;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->dateTimeCreated = new \DateTime();
    }

    public function getRoutes()
    {
        return $this->container->get('router')->getRouteCollection();
    }

    public function getPages()
    {
        return $this->container->get('simplr.repository.page')->findAll();
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeCreated()
    {
        return $this->dateTimeCreated;
    }
}
