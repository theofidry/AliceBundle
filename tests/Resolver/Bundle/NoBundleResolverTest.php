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

namespace Hautelook\AliceBundle\Resolver\Bundle;

use Hautelook\AliceBundle\BundleResolverInterface;
use Hautelook\AliceBundle\Resolver\FakeBundleResolver;
use Hautelook\AliceBundle\Resolver\ResolverKernel;
use function is_a;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @covers \Hautelook\AliceBundle\Resolver\Bundle\NoBundleResolver
 */
class NoBundleResolverTest extends TestCase
{
    use ProphecyTrait;

    public function testIsABundleResolver(): void
    {
        self::assertTrue(is_a(NoBundleResolver::class, BundleResolverInterface::class, true));
    }

    public function testIsNotClonable(): void
    {
        self::assertFalse((new ReflectionClass(NoBundleResolver::class))->isCloneable());
    }

    public function testReturnsAllBundlesIfNoBundleIsRequested(): void
    {
        $kernel = new ResolverKernel(__FUNCTION__, true);
        $kernel->boot();
        $application = new Application($kernel);

        $resolver = new NoBundleResolver(new FakeBundleResolver());
        $result = $resolver->resolveBundles($application, []);
        self::assertSame(
            [
                $kernel->getBundle('ABundle'),
                $kernel->getBundle('BBundle'),
            ],
            $result
        );

        $kernel->shutdown();
    }

    public function testReturnsTheDecoratedResolverResultIfABundleIsRequested(): void
    {
        $kernel = new ResolverKernel(__FUNCTION__, true);
        $application = new Application($kernel);
        $bundles = ['ABundle'];

        $decoratedResolverProphecy = $this->prophesize(BundleResolverInterface::class);
        $decoratedResolverProphecy->resolveBundles($application, $bundles)->willReturn($expected = [new \stdClass()]);
        /** @var BundleResolverInterface $decoratedResolver */
        $decoratedResolver = $decoratedResolverProphecy->reveal();

        $resolver = new NoBundleResolver($decoratedResolver);
        $actual = $resolver->resolveBundles($application, $bundles);

        self::assertSame($expected, $actual);

        $decoratedResolverProphecy->resolveBundles(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
