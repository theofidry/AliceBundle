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
use Hautelook\AliceBundle\Resolver\ResolverKernel;
use function is_a;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @covers \Hautelook\AliceBundle\Resolver\Bundle\SimpleBundleResolver
 */
class SimpleBundleResolverTest extends TestCase
{
    public function testIsABundleResolver(): void
    {
        self::assertTrue(is_a(SimpleBundleResolver::class, BundleResolverInterface::class, true));
    }

    public function testIsNotClonable(): void
    {
        self::assertFalse((new ReflectionClass(SimpleBundleResolver::class))->isCloneable());
    }

    public function testCanResolveBundles(): void
    {
        $kernel = new ResolverKernel(__FUNCTION__, true);
        $kernel->boot();
        $application = new Application($kernel);

        $resolver = new SimpleBundleResolver();
        $result = $resolver->resolveBundles($application, ['ABundle']);
        self::assertSame(
            [
                $kernel->getBundle('ABundle'),
            ],
            $result
        );

        $kernel->shutdown();
    }

    public function testThrowsAnExceptionWhenBundleCoudlNotBeFound(): void
    {
        $this->expectException(\Hautelook\AliceBundle\Exception\Resolver\BundleNotFoundException::class);
        $this->expectExceptionMessage('The bundle "UnknownBundle" was not found. Bundles available are: ["ABundle", "BBundle"].');

        $kernel = new ResolverKernel(__FUNCTION__, true);
        $application = new Application($kernel);
        $kernel->boot();

        try {
            $resolver = new SimpleBundleResolver();
            $resolver->resolveBundles($application, ['UnknownBundle']);
        } finally {
            $kernel->shutdown();
        }
    }
}
