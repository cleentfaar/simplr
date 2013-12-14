<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Bundle\SimplrCmsBundle\Tests\Controller\Install;

use Cleentfaar\Bundle\SimplrCmsBundle\Test\FunctionalTest;

class WizardControllerTest extends FunctionalTest
{
    public function testIndex()
    {
        $this->loadDefaultFixtures();
        $client = static::createClient();
        $client->followRedirects(false);
        $crawler = $client->request('GET', '/_install/wizard');
        $this->assertTrue($crawler->filter('html:contains("form.steps")')->count() > 0);
    }
}
