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

namespace Hautelook\AliceBundle\Locator;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use function is_a;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionClass;

/**
 * @covers \Hautelook\AliceBundle\Locator\EnvironmentlessFilesLocator
 */
class EnvironmentlessFilesLocatorTest extends TestCase
{
    use ProphecyTrait;

    public function testIsAFixtureLocator(): void
    {
        self::assertTrue(is_a(EnvironmentlessFilesLocator::class, FixtureLocatorInterface::class, true));
    }

    public function testIsNotClonable(): void
    {
        self::assertFalse((new ReflectionClass(EnvironmentlessFilesLocator::class))->isCloneable());
    }

    public function testConcateneAllLocatorFiles(): void
    {
        $bundles = ['ABdundle', 'BBundle'];
        $env = 'dev';

        /** @var ObjectProphecy<FixtureLocatorInterface> $decoratedLocatorProphecy */
        $decoratedLocatorProphecy = $this->prophesize(FixtureLocatorInterface::class);
        $decoratedLocatorProphecy
            ->locateFiles($bundles, $env)
            ->willReturn(['/path/to/file1.yml', '/path/to/file2.yml'])
        ;
        $decoratedLocatorProphecy
            ->locateFiles($bundles, '')
            ->willReturn(['/path/to/file2.yml', '/path/to/file3.yml'])
        ;
        /** @var FixtureLocatorInterface $decoratedLocator */
        $decoratedLocator = $decoratedLocatorProphecy->reveal();

        $expected = [
            '/path/to/file1.yml',
            '/path/to/file2.yml',
            '/path/to/file3.yml',
        ];

        $locator = new EnvironmentlessFilesLocator($decoratedLocator);
        $actual = $locator->locateFiles($bundles, $env);

        self::assertEquals($expected, $actual);

        $decoratedLocatorProphecy->locateFiles(Argument::cetera())->shouldHaveBeenCalledTimes(2);
    }
}
