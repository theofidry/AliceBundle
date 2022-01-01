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

namespace Hautelook\AliceBundle\Tests\Alice\Loader;

use Hautelook\AliceBundle\Functional\TestBundle\Entity\CityFactory;
use Hautelook\AliceBundle\Functional\TestKernel;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ServiceFactoryTest extends TestCase
{
    public function testCanUseServiceFactory(): void
    {
        $kernel = new TestKernel('ServiceFactoryTest', true);
        $kernel->boot();

        $loader = $kernel->getContainer()->get('nelmio_alice.file_loader');
        $set = $loader->loadFile(__DIR__.'/../../../fixtures/fixture_files/city.yaml');

        $cityFactory = new CityFactory();
        $expected = [];
        for ($i = 0; $i <= 10; ++$i) {
            $expected['city_'.$i] = $cityFactory->create((string) $i);
        }

        self::assertCount(\count($expected), $set->getObjects());
        self::assertEquals($expected, $set->getObjects());

        $kernel->shutdown();
    }
}
