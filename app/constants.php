<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define("SIMPLR_PATHTO_ROOT",        realpath(__DIR__ . "/../"));
define("SIMPLR_PATHTO_APP",         SIMPLR_PATHTO_ROOT . "/app");
define("SIMPLR_PATHTO_LIBRARY",     SIMPLR_PATHTO_ROOT . "/src/Simplr");
define("SIMPLR_PATHTO_CONFIG",      SIMPLR_PATHTO_APP . "/config");
define("SIMPLR_PATHTO_CACHE",       SIMPLR_PATHTO_APP . "/tmp/cache");
define("SIMPLR_PATHTO_LOGS",        SIMPLR_PATHTO_APP . "/tmp/logs");
define("SIMPLR_PATHTO_THEMES",      SIMPLR_PATHTO_APP . "/themes");
define("SIMPLR_PATHTO_PLUGINS",     SIMPLR_PATHTO_APP . "/plugins");
define("SIMPLR_PATHTO_WEB",         SIMPLR_PATHTO_ROOT . "/web");
define("SIMPLR_PATHTO_ASSETS",      SIMPLR_PATHTO_WEB . "/assets");
define("SIMPLR_URITO_MEDIA",        "/assets/media");
