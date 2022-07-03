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

use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Fidry\AliceDataFixtures\Loader\FileResolverLoader;
use Fidry\AliceDataFixtures\LoaderInterface;
use Hautelook\AliceBundle\BundleResolverInterface;
use Hautelook\AliceBundle\Console\Command\Doctrine\DoctrineOrmLoadDataFixturesCommand;
use Hautelook\AliceBundle\FixtureLocatorInterface;
use Hautelook\AliceBundle\Functional\AppKernel;
use Hautelook\AliceBundle\Functional\ConfigurableKernel;
use Hautelook\AliceBundle\Functional\WithoutDoctrineKernel;
use Hautelook\AliceBundle\Loader\DoctrineOrmLoader;
use Hautelook\AliceBundle\LoaderInterface as HautelookLoaderInterface;
use Hautelook\AliceBundle\Locator\EnvDirectoryLocator;
use Hautelook\AliceBundle\Locator\EnvironmentlessFilesLocator;
use Hautelook\AliceBundle\Resolver\Bundle\NoBundleResolver;
use Hautelook\AliceBundle\Resolver\Bundle\SimpleBundleResolver;
use LogicException;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \Hautelook\AliceBundle\HautelookAliceBundle
 * @covers \Hautelook\AliceBundle\DependencyInjection\Configuration
 * @covers \Hautelook\AliceBundle\DependencyInjection\HautelookAliceExtension
 */
class HautelookAliceBundleTest extends KernelTestCase
{
    public function testCannotBootIfFidryAliceDataFixturesBundleIsNotRegistered(): void
    {
        $kernel = new ConfigurableKernel('ConfigurableKernel0', true);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('To register "Hautelook\AliceBundle\HautelookAliceBundle", you also need: "Doctrine\Bundle\DoctrineBundle\DoctrineBundle", "Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle".');

        $kernel->boot();
    }

    public function testWillReplaceFixtureLoadCommandWithErrorInformationCommandIfDoctrineBundleIsNotRegistered(): void
    {
        $kernel = new WithoutDoctrineKernel('ConfigurableKernel1', true);
        $kernel->addBundle(new FidryAliceDataFixturesBundle());
        $kernel->addBundle(new NelmioAliceBundle());

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('To register "Hautelook\AliceBundle\HautelookAliceBundle", you also need: "Doctrine\Bundle\DoctrineBundle\DoctrineBundle".');

        $kernel->boot();
    }

    public function testServiceRegistration(): void
    {
        self::bootKernel(['environment' => 'public', 'debug' => true]);
        $container = self::getContainer();

        // Resolvers
        self::assertInstanceOf(
            SimpleBundleResolver::class,
            $container->get('hautelook_alice.resolver.bundle.simple_resolver')
        );

        self::assertInstanceOf(
            NoBundleResolver::class,
            $container->get('hautelook_alice.resolver.bundle.no_bundle_resolver')
        );

        self::assertInstanceOf(
            BundleResolverInterface::class,
            $container->get('hautelook_alice.resolver.bundle')
        );

        // Locators
        self::assertInstanceOf(
            EnvironmentlessFilesLocator::class,
            $container->get('hautelook_alice.locator.environmentless')
        );

        self::assertInstanceOf(
            EnvDirectoryLocator::class,
            $container->get('hautelook_alice.locator.env_directory')
        );

        self::assertInstanceOf(
            FixtureLocatorInterface::class,
            $container->get('hautelook_alice.locator')
        );

        // Loader
        self::assertInstanceOf(
            FileResolverLoader::class,
            $container->get('hautelook_alice.data_fixtures.loader.file_resolver_loader')
        );

        self::assertInstanceOf(
            LoaderInterface::class,
            $container->get('hautelook_alice.data_fixtures.purge_loader')
        );

        self::assertInstanceOf(
            LoaderInterface::class,
            $container->get('hautelook_alice.data_fixtures.append_loader')
        );

        self::assertInstanceOf(
            DoctrineOrmLoader::class,
            $container->get('hautelook_alice.loader.doctrine_orm_loader')
        );

        self::assertInstanceOf(
            HautelookLoaderInterface::class,
            $container->get('hautelook_alice.loader')
        );

        // Commands
        self::assertInstanceOf(
            DoctrineOrmLoadDataFixturesCommand::class,
            $container->get('hautelook_alice.console.command.doctrine.doctrine_orm_load_data_fixtures_command')
        );
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
