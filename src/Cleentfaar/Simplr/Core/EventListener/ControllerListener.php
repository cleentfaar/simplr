<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\EventListener;

use Cleentfaar\Simplr\Core\Controller\BaseController;
use Cleentfaar\Simplr\Core\Controller\BaseInstallController;
use Cleentfaar\Simplr\Core\Exception\NotInstalledException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ControllerListener
{

    const INSTALLATION_ROUTE_PREFIX = 'simplr_install_';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
        * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
        * If it is a class, it comes in array format
        */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof BaseController) {
            $request = $event->getRequest();
            $route = $request->get('_route');
            if (substr($route, 0, strlen(self::INSTALLATION_ROUTE_PREFIX)) !== self::INSTALLATION_ROUTE_PREFIX &&
                $this->container->get('simplr.instance')->isInstalled() === false
            ) {
                /**
                 * @var UrlGenerator $generator
                 */
                $event->stopPropagation();
                throw new NotInstalledException();
            }
            if ($controller[0] instanceof BaseInstallController) {
                if ($this->container->get('simplr.instance')->isInstalled() === true) {
                    throw new \Exception("Simplr is already installed");
                }
            } else {
                $this->container->get('simplr.thememanager')->registerActiveTheme(
                    $this->container->get('event_dispatcher')
                );
                $this->container->get('simplr.pluginmanager')->registerActivePlugins(
                    $this->container->get('event_dispatcher')
                );
            }
        }
    }
}
