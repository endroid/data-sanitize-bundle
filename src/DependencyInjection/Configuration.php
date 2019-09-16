<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('endroid_data_sanitize');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->arrayNode('entities')->prototype('array')
                    ->children()
                        ->scalarNode('class')->isRequired()->end()
                        ->arrayNode('fields')->prototype('scalar')
        ;

        return $treeBuilder;
    }
}
