<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Tests\Controller;

use Simplr\ApplicationBundle\Test\FunctionalTest;

class DefaultControllerTest extends FunctionalTest
{
    public function testIndex()
    {
        $this->loadFixtures(array('Simplr\ApplicationBundle\DataFixtures\LoadDefaultData'));
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/dashboard');

        $this->assertTrue($crawler->filter('html:contains("Welcome")')->count() > 0);
    }
}
