<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Bundle\CmsBundle\Test;

use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class FunctionalTest extends WebTestCase
{
    protected function loadDefaultFixtures()
    {
        return $this->loadFixtures(array('Cleentfaar\Simplr\Bundle\CmsBundle\DataFixtures\ORM\LoadCmsData'));
    }
}