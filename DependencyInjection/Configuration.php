<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DashboardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Quentin Somazzi <qsomazzi@ekino.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_dashboard')->children();

        $node
            ->arrayNode('class')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('dashboard')->defaultValue('Application\\Sonata\\DashboardBundle\\Entity\\Dashboard')->end()
                    ->scalarNode('block')->defaultValue('Application\\Sonata\\DashboardBundle\\Entity\\Block')->end()
                ->end()
            ->end()
            ->scalarNode('default_container')
                ->defaultValue('sonata.dashboard.block.container')
                ->cannotBeEmpty()
            ->end()
            ->arrayNode('templates')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('compose')
                        ->defaultValue('SonataDashboardBundle:DashboardAdmin:compose.html.twig')
                        ->info('This value sets the composer template.')
                    ->end()
                    ->scalarNode('compose_container_show')
                        ->defaultValue('SonataDashboardBundle:DashboardAdmin:compose_container_show.html.twig')
                        ->info('This value sets the container composer template.')
                    ->end()
                    ->scalarNode('render')
                        ->defaultValue('SonataDashboardBundle:DashboardAdmin:render.html.twig')
                        ->info('This value sets the render template.')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
