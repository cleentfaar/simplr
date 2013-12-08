<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\DataFixtures\ORM;

use Cleentfaar\Simplr\Core\Entity\Page;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Cleentfaar\Simplr\Core\Entity\Option;

class LoadCmsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $option = new Option();
        $option->setName('active_theme');
        $option->setValue('alpha');
        $manager->persist($option);

        $themeOptions = array(
            'logo_type_text' => 'YourSite',
        );
        $option = new Option();
        $option->setName('theme_options_alpha');
        $option->setValue($themeOptions);
        $manager->persist($option);

        $page = new Page();
        $page->setTitle('Example page');
        $page->setContent('Some content here...');
        $page->setTemplate('index.html.twig');
        $page->setSlug('');
        $manager->persist($page);

        $manager->flush();

    }
}
