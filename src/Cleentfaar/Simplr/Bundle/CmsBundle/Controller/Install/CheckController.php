<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Bundle\CmsBundle\Controller\Install;

use Cleentfaar\Simplr\Core\Controller\BaseInstallController;

class CheckController extends BaseInstallController
{
    public function indexAction()
    {
        return $this->render('CleentfaarSimplrCmsBundle:Install:check.html.twig');
    }
}
