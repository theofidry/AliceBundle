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

use Fidry\AliceDataFixtures\Loader\FileResolverLoader;
use Hautelook\AliceBundle\Loader\DoctrineOrmLoader;

return static function (ContainerConfigurator $container) {
    $container->services()
        // Resources
        ->set('hautelook_alice.data_fixtures.loader.file_resolver_loader', FileResolverLoader::class)
            ->args([
                service('fidry_alice_data_fixtures.doctrine.purger_loader'),
                service('hautelook_alice.resolver.file'),
            ])

        ->set('hautelook_alice.data_fixtures.purge_loader', FileResolverLoader::class)
            ->args([
                service('fidry_alice_data_fixtures.loader.doctrine'),
                service('hautelook_alice.resolver.file'),
            ])
            ->public()

        ->set('hautelook_alice.data_fixtures.append_loader', FileResolverLoader::class)
            ->args([
                service('fidry_alice_data_fixtures.doctrine.persister_loader'),
                service('hautelook_alice.resolver.file'),
            ])
            ->public()

        // HautelookAliceBundle
        ->set('hautelook_alice.loader.doctrine_orm_loader', DoctrineOrmLoader::class)
            ->args([
                service('hautelook_alice.resolver.bundle'),
                service('hautelook_alice.locator'),
                service('hautelook_alice.data_fixtures.purge_loader'),
                service('hautelook_alice.data_fixtures.append_loader'),
                service('logger')->nullOnInvalid(),
            ])

        ->alias('hautelook_alice.loader', 'hautelook_alice.loader.doctrine_orm_loader')
            ->public()
    ;
};
