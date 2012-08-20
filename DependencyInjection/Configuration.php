<?php

/*
 * This file is part of the Ekino PHP metric project.
 *
 * (c) Ekino - Thomas Rabaix <thomas.rabaix@ekino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\MetricBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('ekino_metric')
            ->children()
                ->arrayNode('metrics')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')->isRequired()->end()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('type')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('reporter')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('reporters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('collectd')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('hostname')->cannotBeEmpty()->defaultValue(php_uname('n'))->end()
                                ->scalarNode('udp_host')->cannotBeEmpty()->defaultValue('localhost')->end()
                                ->scalarNode('udp_port')->cannotBeEmpty()->defaultValue(25826)->end()
                            ->end()
                        ->end()
                        ->arrayNode('statsd')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('udp_host')->cannotBeEmpty()->defaultValue('localhost')->end()
                                ->scalarNode('udp_port')->cannotBeEmpty()->defaultValue(8125)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}