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
use Hautelook\AliceBundle\Functional\AppKernel;
use Hautelook\AliceBundle\Functional\ConfigurableKernel;
use Hautelook\AliceBundle\Functional\WithoutDoctrineKernel;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @covers \Hautelook\AliceBundle\HautelookAliceBundle
 * @covers \Hautelook\AliceBundle\DependencyInjection\Configuration
 * @covers \Hautelook\AliceBundle\DependencyInjection\HautelookAliceExtension
 */
class HautelookAliceBundleTest extends KernelTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        if (Kernel::VERSION_ID < 41000) {
            if (null !== self::$kernel) {
                self::$kernel->shutdown();
            }
        } else {
            parent::tearDown();
            static::$class = null;
        }
    }

    public function testCannotBootIfFidryAliceDataFixturesBundleIsNotRegistered()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('To register "Hautelook\AliceBundle\HautelookAliceBundle", you also need: "Doctrine\Bundle\DoctrineBundle\DoctrineBundle", "Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle".');

        self::$kernel = new ConfigurableKernel('ConfigurableKernel0', true);
        self::$kernel->boot();
    }

    public function testWillReplaceFixtureLoadCommandWithErrorInformationCommandIfDoctrineBundleIsNotRegistered()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('To register "Hautelook\AliceBundle\HautelookAliceBundle", you also need: "Doctrine\Bundle\DoctrineBundle\DoctrineBundle".');

        self::$kernel = new WithoutDoctrineKernel('ConfigurableKernel1', true);
        self::$kernel->addBundle(new FidryAliceDataFixturesBundle());
        self::$kernel->addBundle(new NelmioAliceBundle());
        self::$kernel->boot();
    }

    public function testServiceRegistration()
    {
        if (Kernel::VERSION_ID < 41000) {
            self::$kernel = new AppKernel('public', true);
            self::$kernel->boot();
            $container = self::$kernel->getContainer();
        } else {
            parent::bootKernel(['environment' => 'public', 'debug' => true]);
            $container = self::$container;
        }

        // Resolvers
        $this->assertInstanceOf(
            \Hautelook\AliceBundle\Resolver\Bundle\SimpleBundleResolver::class,
            $container->get('hautelook_alice.resolver.bundle.simple_resolver')
        );

        $this->assertInstanceOf(
            \Hautelook\AliceBundle\Resolver\Bundle\NoBundleResolver::class,
            $container->get('hautelook_alice.resolver.bundle.no_bundle_resolver')
        );

        $this->assertInstanceOf(
            \Hautelook\AliceBundle\BundleResolverInterface::class,
            $container->get('hautelook_alice.resolver.bundle')
        );

        // Locators
        $this->assertInstanceOf(
            \Hautelook\AliceBundle\Locator\EnvironmentlessFilesLocator::class,
            $container->get('hautelook_alice.locator.environmentless')
        );

        $this->assertInstanceOf(
            \Hautelook\AliceBundle\Locator\EnvDirectoryLocator::class,
            $container->get('hautelook_alice.locator.env_directory')
        );

        $this->assertInstanceOf(
            \Hautelook\AliceBundle\FixtureLocatorInterface::class,
            $container->get('hautelook_alice.locator')
        );

        // Loader
        $this->assertInstanceOf(
            \Fidry\AliceDataFixtures\Loader\FileResolverLoader::class,
            $container->get('hautelook_alice.data_fixtures.loader.file_resolver_loader')
        );

        $this->assertInstanceOf(
            \Fidry\AliceDataFixtures\LoaderInterface::class,
            $container->get('hautelook_alice.data_fixtures.purge_loader')
        );

        $this->assertInstanceOf(
            \Fidry\AliceDataFixtures\LoaderInterface::class,
            $container->get('hautelook_alice.data_fixtures.append_loader')
        );

        $this->assertInstanceOf(
            \Hautelook\AliceBundle\Loader\DoctrineOrmLoader::class,
            $container->get('hautelook_alice.loader.doctrine_orm_loader')
        );

        $this->assertInstanceOf(
            \Hautelook\AliceBundle\LoaderInterface::class,
            $container->get('hautelook_alice.loader')
        );

        // Commands
        $this->assertInstanceOf(
            \Hautelook\AliceBundle\Console\Command\Doctrine\DoctrineOrmLoadDataFixturesCommand::class,
            $container->get('hautelook_alice.console.command.doctrine.doctrine_orm_load_data_fixtures_command')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
