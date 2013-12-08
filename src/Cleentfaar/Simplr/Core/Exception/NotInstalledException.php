<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Exception;

class NotInstalledException extends Exception
{
    public function __construct($message = 'Simplr is not installed', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
