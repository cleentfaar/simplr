<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * @see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        //$rootNode = $treeBuilder->root('simplr');
        // ...
        $treeBuilder->root('simplr');


        return $treeBuilder;
    }

    public function loadServices(ContainerInterface $container, $pathToServicesDir)
    {
        $realPath = realpath($pathToServicesDir);
        if (!$realPath || !is_dir($pathToServicesDir)) {
            throw new \Exception(
                sprintf(
                    "Must supply an existing directory to search for services configuration (attempted: %s)",
                    $pathToServicesDir
                )
            );
        }
        $loader = new YamlFileLoader($container, new FileLocator($realPath));
        $loader->load('services.yml');
    }
}
