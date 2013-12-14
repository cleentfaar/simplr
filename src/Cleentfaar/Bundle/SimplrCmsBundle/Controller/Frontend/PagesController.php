<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Bundle\SimplrCmsBundle\Controller\Frontend;

use Cleentfaar\Simplr\Core\Controller\BaseController;

class PagesController extends BaseController
{
    public function indexAction($slug)
    {
        $page = $this->findByOr404('Simplr:Page', array('slug' => $slug), true);

        return $this->render(
            '@current_theme/'.$page->getTemplate(),
            array('page' => $page)
        );
    }
}
