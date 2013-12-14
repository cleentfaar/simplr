<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Bundle\SimplrCmsBundle\Tests\Controller\Frontend;

use Cleentfaar\Bundle\SimplrCmsBundle\Test\FunctionalTest;

class PagesControllerTest extends FunctionalTest
{
    public function testIndex()
    {
        $this->loadDefaultFixtures();
        $client = static::createClient();

        /**
         * Should be redirected to installation routine
         */
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        $this->assertFalse($crawler->filter('html:contains("Simplr")')->count() > 0);
    }
}
