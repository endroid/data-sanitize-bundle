<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('endroid_data_sanitize');

        $treeBuilder /* @phpstan-ignore-line */
            ->getRootNode()
                ->children()
                    ->arrayNode('entities')
                        ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->arrayNode('fields')->prototype('scalar')->end()->end()
                            ->scalarNode('reference')->isRequired()->defaultValue('id')
        ;

        return $treeBuilder;
    }
}
