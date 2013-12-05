<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\CmsBundle\Events;

class ImageFilterSetsEvent
{
    public function __construct()
    {
        $this->filterSets = array();
    }

    public function getFilterSets()
    {
        return $this->filterSets;
    }

    public function addFilterSet($name, $filters)
    {
        $this->filterSets[$name] = $filters;
    }
}