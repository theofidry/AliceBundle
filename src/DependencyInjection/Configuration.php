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
use Symfony\Component\HttpKernel\Kernel;

/**
 * @private
 *
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('hautelook_alice');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC for symfony/config < 4.2
            $rootNode = $treeBuilder->root('hautelook_alice');
        }

        $defaultRootDirsValue = [
            '%kernel.root_dir%',
            '%kernel.project_dir%',
        ];
        if (Kernel::VERSION_ID >= 50000) {
            $defaultRootDirsValue = [
                '%kernel.project_dir%',
            ];
        }

        $rootNode
            ->children()
                ->arrayNode('fixtures_path')
                    ->info('Path(s) to which to look for fixtures relative to the bundle/root directory paths.')
                    ->beforeNormalization()->castToArray()->end()
                    ->defaultValue(['Resources/fixtures'])
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('root_dirs')
                    ->info('List of root directories into which to look for the fixtures.')
                    ->defaultValue($defaultRootDirsValue)
                    ->scalarPrototype()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
