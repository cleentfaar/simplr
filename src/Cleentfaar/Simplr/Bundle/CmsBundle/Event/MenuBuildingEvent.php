<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Bundle\CmsBundle\Event;

use Knp\Menu\MenuItem;
use Symfony\Component\EventDispatcher\Event;

class MenuBuildingEvent extends Event
{

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $menu;

    /**
     * @param MenuItem $menu
     */
    public function __construct(MenuItem $menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return MenuItem
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
