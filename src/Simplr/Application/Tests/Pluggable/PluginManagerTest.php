<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Application\Tests;

use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;

class PluginManagerTest extends WebTestCase
{
    public function testConstruct()
    {
        $pathToPlugins = '/path/to/plugins';
        if (!is_dir($pathToPlugins)) {
            throw new \Exception(sprintf("Path provided for plugins is not a valid directory (%s)", $pathToPlugins));
        }
    }
}