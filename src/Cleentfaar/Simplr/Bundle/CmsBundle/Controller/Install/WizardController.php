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

class WizardController extends BaseInstallController
{
    public function step1Action()
    {
        return $this->render('CleentfaarSimplrCmsBundle:Install:Wizard/step1.html.twig');
    }
    public function step2Action()
    {
        return $this->render('CleentfaarSimplrCmsBundle:Install:Wizard/step2.html.twig');
    }
}
