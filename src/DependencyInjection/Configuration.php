<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final readonly class Configuration implements ConfigurationInterface
{
    #[\Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        /** @var TreeBuilder<'array'> $treeBuilder */
        $treeBuilder = new TreeBuilder('endroid_data_sanitize', type: 'array');

        $treeBuilder
            ->getRootNode()
            ->children()
            ->arrayNode('entities')
            ->arrayPrototype()
            ->children()
            ->scalarNode('class')
            ->isRequired()
            ->validate()
            ->ifTrue(static fn(mixed $class): bool => !is_string($class) || !class_exists($class))
            ->thenInvalid('The configured entity class "%s" does not exist.')
            ->end()
            ->end()
            ->arrayNode('fields')
            ->scalarPrototype()
            ->validate()
            ->ifTrue(static fn(mixed $field): bool => !is_string($field))
            ->thenInvalid('Field names must be strings.')
            ->end()
            ->end()
            ->end()
            ->scalarNode('reference')
            ->defaultValue('id')
            ->validate()
            ->ifTrue(static fn(mixed $reference): bool => !is_string($reference))
            ->thenInvalid('The reference must be a string.')
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
