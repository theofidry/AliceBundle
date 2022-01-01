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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use ReflectionClass;

/**
 * @covers \Hautelook\AliceBundle\Locator\EnvironmentlessFilesLocator
 */
class EnvironmentlessFilesLocatorTest extends TestCase
{
    public function testIsAFixtureLocator()
    {
        $this->assertTrue(is_a(EnvironmentlessFilesLocator::class, FixtureLocatorInterface::class, true));
    }

    public function testIsNotClonable()
    {
        $this->assertFalse((new ReflectionClass(EnvironmentlessFilesLocator::class))->isCloneable());
    }

    public function testConcateneAllLocatorFiles()
    {
        $bundles = ['ABdundle', 'BBundle'];
        $env = 'dev';

        /** @var FixtureLocatorInterface|ObjectProphecy $decoratedLocatorProphecy */
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

        $this->assertEquals($expected, $actual);

        $decoratedLocatorProphecy->locateFiles(Argument::cetera())->shouldHaveBeenCalledTimes(2);
    }
}
