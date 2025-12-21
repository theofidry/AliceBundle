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

use Hautelook\AliceBundle\Alice\FileLocator\KernelFileLocator;
use Hautelook\AliceBundle\Alice\Generator\Instantiator\Chainable\InstantiatedReferenceInstantiator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('hautelook_alice.alice.generator.instantiator.chainable.instantiated_reference_instantiator', InstantiatedReferenceInstantiator::class)
            ->call('setContainer', [service('service_container')])
            ->tag('nelmio_alice.generator.instantiator.chainable_instantiator')

        ->alias('nelmio_alice.file_locator', 'hautelook_alice.alice.file_locator.kernel')

        ->set('hautelook_alice.alice.file_locator.kernel', KernelFileLocator::class)
            ->args([
                service('nelmio_alice.file_locator.default'),
                service('kernel'),
            ])
    ;
};
