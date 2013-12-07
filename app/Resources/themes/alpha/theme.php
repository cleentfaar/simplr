<?php
namespace Acme\AlphaPlugin;

use Cleentfaar\Simplr\Bundle\CmsBundle\CmsEvents;
use Cleentfaar\Simplr\Bundle\CmsBundle\Event\GetMenuEvent;

return array(
    'description' => 'This is the alpha theme',
    'hooks' => array(
        CmsEvents::GET_BACKEND_SIDEBAR_MENU => function (GetMenuEvent $event) {
            $menu = $event->getMenu();
            $menu->addChild('Test', array('uri' => '#'));
        }
    )
);