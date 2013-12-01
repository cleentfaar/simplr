<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\ApplicationBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Simplr\ApplicationBundle\Entity\Option;

class LoadDefaultData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $option = new Option();
        $option->setName('active_theme');
        $option->setValue('Alpha');

        $manager->persist($option);
        $manager->flush();
    }
}
