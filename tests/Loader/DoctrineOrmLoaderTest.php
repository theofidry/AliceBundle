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

namespace Hautelook\AliceBundle\Loader;

use Hautelook\AliceBundle\DataFixtures\Loader\FakeLoader;
use Hautelook\AliceBundle\FixtureLocator\FakeFixtureLocator;
use Hautelook\AliceBundle\LoaderInterface;
use Hautelook\AliceBundle\Logger\FakeLogger;
use Hautelook\AliceBundle\LoggerAwareInterface;
use Hautelook\AliceBundle\Resolver\FakeBundleResolver;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers \Hautelook\AliceBundle\Loader\DoctrineOrmLoader
 */
class DoctrineOrmLoaderTest extends TestCase
{
    public function testIsALoader()
    {
        $this->assertTrue(is_a(DoctrineOrmLoader::class, LoaderInterface::class, true));
    }

    public function testIsLoggerAware()
    {
        $this->assertTrue(is_a(DoctrineOrmLoader::class, LoggerAwareInterface::class, true));
    }

    public function testIsNotClonable()
    {
        $this->assertFalse((new ReflectionClass(DoctrineOrmLoader::class))->isCloneable());
    }

    public function testDataFixtureLoaderMustBePersisterAware()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected loader to be an instance of "Fidry\AliceDataFixtures\Persistence\PersisterAwareInterface".');

        new DoctrineOrmLoader(new FakeBundleResolver(), new FakeFixtureLocator(), new FakeLoader(), new FakeLoader(), new FakeLogger());
    }
}
