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

abstract class CmsEvents
{
    const GET_ROUTES = 'simplr.event.get_routes';
    const GET_BACKEND_SIDEBAR_MENU = 'simplr.event.get_backend_sidebar_menu';
}
