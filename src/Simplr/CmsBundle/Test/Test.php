<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\CmsBundle\Test;

use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class Test extends WebTestCase
{

    protected function loadDefaultFixtures()
    {
        return $this->loadFixtures(array('Simplr\CmsBundle\DataFixtures\ORM\LoadCmsData'));
    }
}
