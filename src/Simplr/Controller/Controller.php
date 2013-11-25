<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Controller;

use Silex\Application;

class Controller
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function render($path, array $variables = array())
    {
        return $this->app['twig']->render($path, $variables);
    }
}