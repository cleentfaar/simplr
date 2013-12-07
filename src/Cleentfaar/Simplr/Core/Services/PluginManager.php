<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Services;

class PluginManager
{

    private $pathToPlugins;

    public function __construct($pathToPlugins)
    {
        if (!is_dir($pathToPlugins)) {
            throw new \Exception(sprintf("Path provided for plugins is not a valid directory (%s)", $pathToPlugins));
        }
        $this->pathToPlugins = $pathToPlugins;
    }

    public function getActivePlugins()
    {

    }
}
