<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\CmsBundle\DependencyInjection;

use Simplr\CmsBundle\Events;
use Simplr\CmsBundle\Events\ImageFiltersetsEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */

class SimplrCmsExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        //$config = $this->processConfiguration($configuration, $configs);
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        // determine if LiipImagineBundle is registered
        if (isset($bundles['LiipImagineBundle'])) {
            $config = array(
                'filter_sets' => array(
                    'simplr_themes' => array(
                        'filters' => array(
                            'thumbnail' => array(
                                'size' => array(50,50),
                                'mode' => 'outbound'
                            )
                        )
                    )
                )
            );
            foreach ($container->getExtensions() as $name => $extension) {
                switch ($name) {
                    case 'liip_imagine':
                        // set use_acme_goodbye to false in the config of acme_something and acme_other
                        // note that if the user manually configured use_acme_goodbye to true in the
                        // app/config/config.yml then the setting would in the end be true and not false
                        $container->prependExtensionConfig($name, $config);
                        break;
                }
            }
        }
    }
}
