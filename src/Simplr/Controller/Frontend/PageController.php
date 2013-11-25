<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Controller\Frontend;

use Simplr\Controller\Controller;
use Simplr\Entity\Page;

class PageController extends Controller
{
    public function indexAction(Page $page)
    {
        $templates = array(
            $page->getTemplate(),
            '@simplr/'.$page->getTemplate(),
        );
        return $this->app['twig']->resolveTemplate($templates)->render(array('page' => $page));
    }
}