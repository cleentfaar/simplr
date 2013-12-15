<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Bundle\SimplrCmsBundle\Controller\Install;

use Cleentfaar\Simplr\Core\Controller\BaseInstallController;

class UpdateController extends BaseInstallController
{
    public function indexAction()
    {
        return $this->render('@Simplr/Install/Update/index.html.twig');
    }

    public function runAction()
    {
        return $this->render('@Simplr/Install/Update/run.html.twig');
    }
}
