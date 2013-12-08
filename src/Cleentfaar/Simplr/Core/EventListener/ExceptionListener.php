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

use Cleentfaar\Simplr\Core\Exception\NotInstalledException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class ExceptionListener
{

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGenerator
     */
    private $urlGenerator;

    /**
     * @param RouterInterface $router
     */
    public function __construct(Router $router)
    {
        /**
         * @var UrlGenerator $urlGenerator
         */
        $urlGenerator = $router->getGenerator();
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotInstalledException) {
            $response = new RedirectResponse($this->urlGenerator->generate('simplr_install_welcome'));
            $event->setResponse($response);
            return;
        }
    }
}