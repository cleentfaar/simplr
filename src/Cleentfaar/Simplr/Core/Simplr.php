<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Simplr
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function isInstalled()
    {
        $installationLockPath = $this->getInstallationLockPath();
        return $installationLockPath === null ? true : false;
    }

    public function getInstallationLockPath()
    {
        $path = realpath($this->container->getParameter('kernel.root_dir') . '/NOT_INSTALLED.lock') ;
        if ($path !== false) {
            return $path;
        }
        return null;
    }
}
