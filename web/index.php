<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

ini_set('display_errors', 0);

require_once __DIR__.'/../app/bootstrap.php';
$app = require_once __DIR__.'/../app/services.php';
require_once __DIR__.'/../app/routes.php';

$app->run();
