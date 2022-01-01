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

namespace Hautelook\AliceBundle\Functional;

use function array_flip;
use function array_key_exists;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\String\Slugger\SluggerInterface;

final class MakeServicesPublicCompilerPass implements CompilerPassInterface
{
    private const IGNORED_SERVICE_IDS = [
        'slugger',
        SluggerInterface::class,
        'test.private_services_locator',
    ];

    public function process(ContainerBuilder $container): void
    {
        self::setDefinitionsAsPublic(
            $container->getDefinitions(),
            $container->getAliases(),
        );
    }

    /**
     * @param list<array<string, Definition|mixed>> $sources
     */
    private static function setDefinitionsAsPublic(array $sources): void
    {
        $ignoredServiceIdsAsKeys = array_flip(self::IGNORED_SERVICE_IDS);

        foreach ($sources as $source) {
            foreach ($source as $id => $definition) {
                if ($definition instanceof Definition
                    && !array_key_exists($id, $ignoredServiceIdsAsKeys)
                ) {
                    $definition->setPublic(true);
                }
            }
        }
    }
}
