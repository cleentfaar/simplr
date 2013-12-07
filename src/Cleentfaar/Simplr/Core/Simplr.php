<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core;

class Simplr
{
    /**
     * @var string
     */
    private $pathToWeb;

    public function __construct($pathToWeb)
    {
        if (!is_dir($pathToWeb)) {
            throw new \Exception(sprintf("Path provided for the web directory does not exist (%s)", $pathToWeb));
        }
        $this->pathToWeb = $pathToWeb;
    }

    public function isInstalled()
    {
        $installationLockPath = $this->getInstallationLockPath();
        return $installationLockPath === null ? true : false;
    }

    public function getInstallationLockPath()
    {
        $path = realpath($this->pathToWeb . '/NOT_INSTALLED') ;
        if ($path !== false) {
            return $path;
        }
        return null;
    }
}
