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

use Hautelook\AliceBundle\Console\Command\Doctrine\DoctrineOrmLoadDataFixturesCommand;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('hautelook_alice.console.command.doctrine.doctrine_orm_load_data_fixtures_command', DoctrineOrmLoadDataFixturesCommand::class)
            ->args([
                'hautelook:fixtures:load',
                service('doctrine'),
                service('hautelook_alice.loader')
            ])
            ->public()
            ->tag('console.command')
    ;
};
