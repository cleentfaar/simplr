<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Bundle\CmsBundle\Tests\Controller\Frontend;

use Cleentfaar\Simplr\Bundle\CmsBundle\Test\FunctionalTest;

class PagesControllerTest extends FunctionalTest
{
    public function testIndex()
    {
        $this->loadDefaultFixtures();
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('html:contains("Welcome")')->count() > 0);
    }
}
