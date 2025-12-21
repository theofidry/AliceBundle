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

use Hautelook\AliceBundle\Locator\EnvDirectoryLocator;
use Hautelook\AliceBundle\Locator\EnvironmentlessFilesLocator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('hautelook_alice.locator.env_directory', EnvDirectoryLocator::class)
            ->args([
                param('hautelook_alice.fixtures_path'),
                param('hautelook_alice.root_dirs'),
            ])

        ->set('hautelook_alice.locator.environmentless', EnvironmentlessFilesLocator::class)
            ->args([
                service('hautelook_alice.locator.env_directory'),
            ])

        ->alias('hautelook_alice.locator', 'hautelook_alice.locator.environmentless')
            ->public()
    ;
};
