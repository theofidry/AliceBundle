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

namespace Hautelook\AliceBundle\Console\Command\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use DomainException;
use Hautelook\AliceBundle\HttpKernel\DummyKernel;
use Hautelook\AliceBundle\Loader\FakeLoader;
use Hautelook\AliceBundle\LoaderInterface;
use Hautelook\AliceBundle\Persistence\FakeDoctrineManagerRegistry;
use Hautelook\AliceBundle\Persistence\ObjectMapper\FakeEntityManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Console\Application as FrameworkBundleConsoleApplication;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \Hautelook\AliceBundle\Console\Command\Doctrine\DoctrineOrmLoadDataFixturesCommand
 * @group legacy
 */
class LoadDataFixturesCommandTest extends TestCase
{
    use ProphecyTrait;

    public function testIsACommand(): void
    {
        self::assertTrue(is_a(DoctrineOrmLoadDataFixturesCommand::class, Command::class, true));
    }

    public function testCanSetTheCommandApplication(): void
    {
        $application = new FrameworkBundleConsoleApplication(new DummyKernel());

        $command = new DoctrineOrmLoadDataFixturesCommand('dummy', new FakeDoctrineManagerRegistry(), new FakeLoader());
        $command->setApplication($application);

        self::assertSame($application, $command->getApplication());
    }

    public function testCanResetTheCommandApplication(): void
    {
        $command = new DoctrineOrmLoadDataFixturesCommand('dummy', new FakeDoctrineManagerRegistry(), new FakeLoader());
        $command->setApplication(null);

        self::assertTrue(true);
    }

    public function testThrowsAnExceptionIfInvalidApplicationIsGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected application to be an instance of "Symfony\Bundle\FrameworkBundle\Console\Application".');

        $command = new DoctrineOrmLoadDataFixturesCommand('dummy', new FakeDoctrineManagerRegistry(), new FakeLoader());
        $command->setApplication(new ConsoleApplication());
    }

    /**
     * @group legacy
     */
    public function testCallCommandWithoutArguments(): void
    {
        $application = new FrameworkBundleConsoleApplication(new DummyKernel());
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'hautelook:fixtures:load',
        ]);
        $input->setInteractive(false);

        /** @var ObjectProphecy<ManagerRegistry> $managerRegistryProphecy */
        $managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $managerRegistryProphecy->getManager(null)->willReturn($manager = new FakeEntityManager());
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $managerRegistryProphecy->reveal();

        /** @var ObjectProphecy<LoaderInterface> $loaderProphecy */
        $loaderProphecy = $this->prophesize(LoaderInterface::class);
        $loaderProphecy
            ->load($application, $manager, [], 'fake_env', false, false, null, false)
            ->shouldBeCalled()
        ;
        /** @var LoaderInterface $loader */
        $loader = $loaderProphecy->reveal();

        $command = new DoctrineOrmLoadDataFixturesCommand('dummy', $managerRegistry, $loader);
        $command->setApplication($application);
        $exit = $command->run($input, new NullOutput());

        self::assertEquals(0, $exit);
        $loaderProphecy->load(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $managerRegistryProphecy->getManager(Argument::any())->shouldHaveBeenCalledTimes(1);
    }

    public function testCallCommandWithArguments(): void
    {
        $application = new FrameworkBundleConsoleApplication(new DummyKernel());
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'hautelook:fixtures:load',
            '--manager' => 'DummyManager',
            '--env' => 'dummy_env',
            '--bundle' => [
                'ABundle',
                'BBundle',
            ],
            '--shard' => 'shard_id',
            '--append' => null,
            '--purge-with-truncate' => null,
        ]);
        $input->setInteractive(false);

        /** @var ObjectProphecy<ManagerRegistry> $managerRegistryProphecy */
        $managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $managerRegistryProphecy->getManager('DummyManager')->willReturn($manager = new FakeEntityManager());
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $managerRegistryProphecy->reveal();

        /** @var ObjectProphecy<LoaderInterface> $loaderProphecy */
        $loaderProphecy = $this->prophesize(LoaderInterface::class);
        $loaderProphecy
            ->load($application, $manager, ['ABundle', 'BBundle'], 'dummy_env', true, true, 'shard_id', false)
            ->shouldBeCalled();

        /** @var LoaderInterface $loader */
        $loader = $loaderProphecy->reveal();

        $command = new DoctrineOrmLoadDataFixturesCommand('dummy', $managerRegistry, $loader);
        $command->setApplication($application);
        $exit = $command->run($input, new NullOutput());

        self::assertEquals(0, $exit);
        $loaderProphecy->load(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $managerRegistryProphecy->getManager(Argument::any())->shouldHaveBeenCalledTimes(1);
    }

    public function testCallCommandWithBundleAndNoBundlesFlags(): void
    {
        $application = new FrameworkBundleConsoleApplication(new DummyKernel());
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'hautelook:fixtures:load',
            '--manager' => 'DummyManager',
            '--env' => 'dummy_env',
            '--bundle' => [
                'ABundle',
                'BBundle',
            ],
            '--shard' => 'shard_id',
            '--append' => null,
            '--purge-with-truncate' => null,
            '--no-bundles' => null,
        ]);
        $input->setInteractive(false);

        /** @var ObjectProphecy<ManagerRegistry> $managerRegistryProphecy */
        $managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $managerRegistryProphecy->getManager('DummyManager')->willReturn($manager = new FakeEntityManager());
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $managerRegistryProphecy->reveal();

        /** @var ObjectProphecy<LoaderInterface> $loaderProphecy */
        $loaderProphecy = $this->prophesize(LoaderInterface::class);
        $loaderProphecy
            ->load($application, $manager, ['ABundle', 'BBundle'], 'dummy_env', true, true, 'shard_id', true)
            ->shouldBeCalledTimes(0);

        /** @var LoaderInterface $loader */
        $loader = $loaderProphecy->reveal();

        $command = new DoctrineOrmLoadDataFixturesCommand('dummy', $managerRegistry, $loader);
        $command->setApplication($application);

        try {
            $command->run($input, new NullOutput());

            $this->fail();
        } catch (DomainException $exception) {
            self::assertSame(
                '--bundle and --no-bundles flags cannot be specified both in same time.',
                $exception->getMessage()
            );
        }
    }
}
