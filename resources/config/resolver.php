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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hautelook\AliceBundle\Resolver\Bundle\NoBundleResolver;
use Hautelook\AliceBundle\Resolver\Bundle\SimpleBundleResolver;
use Hautelook\AliceBundle\Resolver\File\KernelFileResolver;

return static function (ContainerConfigurator $container) {
    $container->services()
        // Bundle
        ->set('hautelook_alice.resolver.bundle.simple_resolver', SimpleBundleResolver::class)
            ->args([
                service('kernel'),
            ])

        ->set('hautelook_alice.resolver.bundle.no_bundle_resolver', NoBundleResolver::class)
            ->args([
                service('hautelook_alice.resolver.bundle.simple_resolver'),
            ])

        ->alias('hautelook_alice.resolver.bundle', 'hautelook_alice.resolver.bundle.no_bundle_resolver')
            ->public()

        // File
        ->set('hautelook_alice.resolver.file.kernel_file_resolver', KernelFileResolver::class)
            ->args([
                service('kernel')
            ])

        ->alias('hautelook_alice.resolver.file', 'hautelook_alice.resolver.file.kernel_file_resolver')
            ->public()
    ;
};
