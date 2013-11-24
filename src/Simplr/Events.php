<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr;

abstract class Events
{
    const GET_BACKEND_ROUTES = 'simplr_events.get_backend_routes';
    const GET_FRONTEND_ROUTES = 'simplr_events.get_frontend_routes';
    const GET_AVAILABLE_TEMPLATES = 'simplr_events.get_available_templates';
    const GET_BACKEND_SIDEBAR_ITEMS = 'simplr_events.get_backend_sidebar_items';
}