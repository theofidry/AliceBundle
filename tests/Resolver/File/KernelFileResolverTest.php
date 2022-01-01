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

namespace Hautelook\AliceBundle\Resolver\File;

use Fidry\AliceDataFixtures\FileResolverInterface;
use Hautelook\AliceBundle\Functional\SimpleKernel;
use Hautelook\AliceBundle\HttpKernel\DummyKernel;
use InvalidArgumentException;
use function is_a;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use function realpath;
use ReflectionClass;
use Symfony\Component\HttpKernel\KernelInterface;
use TypeError;

/**
 * @covers \Hautelook\AliceBundle\Resolver\File\KernelFileResolver
 */
class KernelFileResolverTest extends TestCase
{
    use ProphecyTrait;

    protected KernelInterface $kernel;

    protected function tearDown(): void
    {
        if (isset($this->kernel)) {
            $this->kernel->shutdown();
        }
    }

    public function testIsAFileResolver(): void
    {
        self::assertTrue(is_a(KernelFileResolver::class, FileResolverInterface::class, true));
    }

    public function testIsNotClonable(): void
    {
        self::assertFalse((new ReflectionClass(KernelFileResolver::class))->isCloneable());
    }

    public function testReturnResolvedFiles(): void
    {
        $files = $expected = [
            __FILE__,
        ];

        $resolver = new KernelFileResolver(new DummyKernel());
        $actual = $resolver->resolve($files);

        self::assertSame($expected, $actual);
    }

    public function testThrowsAnErrorIfOneOfTheFilePathGivenIsNotAString(): void
    {
        $this->expectException(TypeError::class);

        $resolver = new KernelFileResolver(new DummyKernel());
        $resolver->resolve([true]);
    }

    public function testThrowsAnExceptionIfFileDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file "unknown" was not found.');

        $resolver = new KernelFileResolver(new DummyKernel());
        $resolver->resolve(['unknown']);
    }

    public function testThrowsAnExceptionIfFileIsADirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Expected "\/.*?\/tests\/Resolver\/File" to be a fixture file, got a directory instead\.$/');

        $resolver = new KernelFileResolver(new DummyKernel());
        $resolver->resolve([__DIR__]);
    }

    public function testThrowsAnExceptionIfFileResolvedByTheKernelDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $files = [
            '@SimpleBundle/dummy.yml',
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->boot();

        $resolver = new KernelFileResolver($this->kernel);
        $resolver->resolve($files);
    }

    public function testThrowsAnExceptionIfFileResolvedByTheKernelIsADirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "@SimpleBundle/files" to be a fixture file, got a directory instead.');

        $files = [
            '@SimpleBundle/files',
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->boot();

        $resolver = new KernelFileResolver($this->kernel);
        $resolver->resolve($files);
    }

    public function testAbsoluteFilePathAreReturnedAndDuplicatesAreRemoved(): void
    {
        $files = [
            '@SimpleBundle/files/foo.yaml',
            '@SimpleBundle/files/bar.yaml',
            __FILE__,
            __DIR__.'/../File/KernelFileResolverTest.php',
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->boot();

        $expected = [
            realpath(__DIR__.'/../../../fixtures/Functional/SimpleBundle/files/foo.yaml'),
            realpath(__DIR__.'/../../../fixtures/Functional/SimpleBundle/files/bar.yaml'),
            __FILE__,
        ];

        $resolver = new KernelFileResolver($this->kernel);
        $actual = $resolver->resolve($files);

        self::assertSame($expected, $actual);
    }
}
