<?php
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