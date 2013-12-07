<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Bundle\CmsBundle\Controller\Backend;

use Cleentfaar\Simplr\Core\Controller\BaseController;

class DashboardController extends BaseController
{
    public function indexAction()
    {
        return $this->render('CleentfaarSimplrCmsBundle:Backend/Dashboard:index.html.twig');
    }
}
