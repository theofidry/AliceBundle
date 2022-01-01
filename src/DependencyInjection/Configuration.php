<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Hautelook\AliceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @private
 *
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hautelook_alice');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('fixtures_path')
                    ->info('Path(s) to which to look for fixtures relative to the bundle/root directory paths.')
                    ->beforeNormalization()->castToArray()->end()
                    ->defaultValue(['Resources/fixtures'])
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('root_dirs')
                    ->info('List of root directories into which to look for the fixtures.')
                    ->defaultValue([
                        '%kernel.project_dir%',
                    ])
                    ->scalarPrototype()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
