<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\Test;

abstract class FunctionalTest extends Test
{
    /**
     * Example using LiipFunctionalBundle the fixture loader
     */
    /**
    public function testUserFooIndex()
    {
        $this->loadFixtures(array('Liip\FooBundle\Tests\Fixtures\LoadUserData'));

        $client = $this->createClient();
        $crawler = $client->request('GET', '/users/foo');

        $this->assertTrue($crawler->filter('html:contains("Email: foo@bar.com")')->count() > 0);
    }
     */
}
