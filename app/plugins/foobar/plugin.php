<?php
namespace Acme\FoobarPlugin;

use Silex\Route;
use Simplr\Event\GetRoutesEvent;
use Simplr\Events;
use Symfony\Component\HttpFoundation\Response;

$config = array(
    'title'         => 'Foobar Plugin',
    'description'   => 'My example plugin',
    'version'       => '1.0',
    'hooks'         => array(
        Events::GET_BACKEND_ROUTES => function(GetRoutesEvent $event) {
            $event->add('my_backend_route', new Route('/someurl', array('_controller' => function() {
                return new Response("TEST!");
            })));
        },
    ),
);
return $config;