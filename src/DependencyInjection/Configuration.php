<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /** @psalm-suppress PossiblyUndefinedMethod */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        /** @psalm-suppress TooManyArguments */
        $treeBuilder = new TreeBuilder('endroid_data_sanitize');

        if (method_exists($treeBuilder, 'root')) {
            $rootNode = $treeBuilder->root('endroid_data_sanitize');
        } else {
            /** @psalm-suppress UndefinedMethod */
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
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
