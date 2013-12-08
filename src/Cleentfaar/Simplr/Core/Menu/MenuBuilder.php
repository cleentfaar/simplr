<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Menu;

use Cleentfaar\Simplr\Core\CmsEvents;
use Cleentfaar\Simplr\Core\Event\GetMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher
    ) {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param  Request                 $request
     * @return \Knp\Menu\ItemInterface
     */
    public function createTopMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        /**
         * @todo Add top menu items following theme constraints
         */
        /**
        $event = new MenuBuildingEvent($menu);
        $this->dispatcher->dispatch(CmsEvents::GET_BACKEND_SIDEBAR_MENU, $event);
        return $event->getMenu();
         */
        return $menu;
    }

    /**
     * @param  Request                 $request
     * @return \Knp\Menu\ItemInterface
     */
    public function createSidebarMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild(
            $this->translator->trans('Dashboard'),
            array(
                'route' => 'simplr_backend_dashboard',
                'extras' => array(
                    'icon' => 'dashboard fa-2x',
                ),
            )
        );
        /**
        $pages = $menu->addChild(
            $this->translator->trans('Pages'),
            array(
                'uri' => '#',
                'extras' => array(
                    'icon' => 'files-o fa-2x',
                ),
            )
        );
        $pages->addChild(
            $this->translator->trans('Overview'),
            array(
                'route' => 'simplr_backend_pages',
            )
        );
        $pages->addChild(
            $this->translator->trans('Add new page'),
            array(
                'route' => 'simplr_backend_pages_create',
            )
        );
        $plugins = $menu->addChild(
            $this->translator->trans('Plugins'),
            array(
                'uri' => '#',
                'extras' => array(
                    'icon' => 'link fa-2x',
                ),
            )
        );
        $plugins->addChild(
            $this->translator->trans('Overview'),
            array(
                'route' => 'simplr_backend_plugins',
            )
        );
        $plugins->addChild(
            $this->translator->trans('Install plugin'),
            array(
                'route' => 'simplr_backend_plugins_install',
            )
        );
        $display = $menu->addChild(
            $this->translator->trans('Display'),
            array(
                'uri' => '#',
                'extras' => array(
                    'icon' => 'desktop fa-2x',
                ),
            )
        );
        $themes = $display->addChild(
            $this->translator->trans('Themes'),
            array(
                'uri' => '#',
            )
        );
        $themes->addChild(
            $this->translator->trans('Overview'),
            array(
                'route' => 'simplr_backend_themes',
            )
        );
        $themes->addChild(
            $this->translator->trans('Install theme'),
            array(
                'route' => 'simplr_backend_themes_install',
            )
        );
        $widgets = $display->addChild(
            $this->translator->trans('Widgets'),
            array(
                'route' => 'simplr_backend_widgets',
            )
        );
        $settings = $menu->addChild(
            $this->translator->trans('Settings'),
            array(
                'uri' => '#',
                'extras' => array(
                    'icon' => 'gears',
                ),
            )
        );
        $settings->addChild(
            $this->translator->trans('Generic'),
            array(
                'route' => 'simplr_backend_settings',
            )
        );
        $settings->addChild(
            $this->translator->trans('Users'),
            array(
                'route' => 'simplr_backend_users',
            )
        );
        $settings->addChild(
            $this->translator->trans('Roles'),
            array(
                'route' => 'simplr_backend_roles',
            )
        );
        */

        $event = new GetMenuEvent($menu);
        $this->dispatcher->dispatch(CmsEvents::GET_BACKEND_SIDEBAR_MENU, $event);
        return $event->getMenu();
    }
}
