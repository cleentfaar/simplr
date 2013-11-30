<?php

namespace Simplr\ApplicationBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/dashboard');

        $this->assertTrue($crawler->filter('html:contains("DASHBOARD")')->count() > 0);
    }
}
