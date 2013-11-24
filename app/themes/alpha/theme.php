<?php
namespace Acme\AlphaTheme;

use Simplr\Event\GetRoutesEvent;
use Simplr\Events;
use Symfony\Component\HttpFoundation\Response;

$config = array(
    'title'         => 'The Alpha Theme',
    'description'   => 'My example theme',
    'version'       => '1.0',
    'hooks'         => array(
        Events::GET_BACKEND_ROUTES => function(GetRoutesEvent $event) {
            $event->add('my_backend_route', new \Silex\Route('/someurl', array('_controller' => function() {
                return new Response("TEST!");
            })));
        },
    ),
);
return $config;