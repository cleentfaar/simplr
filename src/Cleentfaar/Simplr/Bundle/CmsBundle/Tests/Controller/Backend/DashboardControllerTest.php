<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Bundle\CmsBundle\Tests\Controller\Backend;

use Cleentfaar\Simplr\Bundle\CmsBundle\Test\FunctionalTest;

class DashboardControllerTest extends FunctionalTest
{
    public function testIndex()
    {
        $this->loadDefaultFixtures();
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/dashboard');

        $this->assertTrue($crawler->filter('html:contains("Overview")')->count() > 0);
    }
}
