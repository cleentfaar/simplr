<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller
{
    public function indexAction($slug)
    {
        $slug = '/' . ltrim($slug, '/');
        $page = $this->getDoctrine()->getManager()->getRepository('SimplrApplicationBundle:Page')->findOneBy(array('slug' => $slug));
        if ($page === null) {
            throw $this->createNotFoundException(sprintf("Could not find page with slug '%s'", $slug));
        }
        return $this->render('@current_theme/index.html.twig', array('page' => $page));
    }
}
