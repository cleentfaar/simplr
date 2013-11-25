<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use Simplr\Controller\Frontend\ImageResizeController;
use Simplr\Controller\Frontend\PageController;
use Simplr\Event\GetRoutesEvent;
use Simplr\Events;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

$app['pages.controller'] = $app->share(function() use ($app) {
    return new PageController($app);
});
$app['imageresize.controller'] = $app->share(function() use ($app) {
    return new ImageResizeController($app);
});
$app->get('/assets/media/images/{path}', 'imageresize.controller:resizeAction');
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
    $event = new GetRoutesEvent();
    $app['dispatcher']->dispatch(Events::GET_BACKEND_ROUTES, $event);
    $backendCollection = $event->getCollection();
    $backendCollection->addPrefix($app['config']['simplr']['backend_prefix']);
    $routes->addCollection($backendCollection);

    $event = new GetRoutesEvent();
    $app['dispatcher']->dispatch(Events::GET_FRONTEND_ROUTES, $event);
    $frontendCollection = $event->getCollection();
    $frontendCollection->addCollection($app['simplr_pagemanager']->getActivePageRoutes());
    $routes->addCollection($frontendCollection);

    return $routes;
});

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['config']['simplr']['debug'] === true) {
        throw $e;
    }

    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
        '@simplr/errors/'.$code.'.html.twig',
        '@simplr/errors/'.substr($code, 0, 2).'x.html.twig',
        '@simplr/errors/'.substr($code, 0, 1).'xx.html.twig',
        '@simplr/errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
