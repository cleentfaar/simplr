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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface Pluggable
{
    public function getConfiguration();
    public function register(EventDispatcherInterface $dispatcher);
}
