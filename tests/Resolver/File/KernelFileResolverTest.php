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
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @covers \Hautelook\AliceBundle\Resolver\File\KernelFileResolver
 */
class KernelFileResolverTest extends TestCase
{
    /**
     * @var KernelInterface|null
     */
    protected $kernel;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        if (null !== $this->kernel) {
            $this->kernel->shutdown();
        }
    }

    public function testIsAFileResolver()
    {
        $this->assertTrue(is_a(KernelFileResolver::class, FileResolverInterface::class, true));
    }

    public function testIsNotClonable()
    {
        $this->assertFalse((new ReflectionClass(KernelFileResolver::class))->isCloneable());
    }

    public function testReturnResolvedFiles()
    {
        $files = $expected = [
            __FILE__,
        ];

        $resolver = new KernelFileResolver(new DummyKernel());
        $actual = $resolver->resolve($files);

        $this->assertSame($expected, $actual);
    }

    public function testThrowsAnErrorIfOneOfTheFilePathGivenIsNotAString()
    {
        $this->expectException(\TypeError::class);

        $resolver = new KernelFileResolver(new DummyKernel());
        $resolver->resolve([true]);
    }

    public function testThrowsAnExceptionIfFileDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The file "unknown" was not found.');

        $resolver = new KernelFileResolver(new DummyKernel());
        $resolver->resolve(['unknown']);
    }

    public function testThrowsAnExceptionIfFileIsADirectory()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Expected "\/.*?\/tests\/Resolver\/File" to be a fixture file, got a directory instead\.$/');

        $resolver = new KernelFileResolver(new DummyKernel());
        $resolver->resolve([__DIR__]);
    }

    public function testResolveFileWithTheKernelIfPossible()
    {
        if (Kernel::VERSION_ID >= 50000) {
            $this->markTestSkipped('Symfony 5 dropped the $first parameter in the locateResource method. This method can no longer return an array');
        }

        $files = [
            '@SimpleBundle/files/foo.yml',
            __FILE__,
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->boot();

        $expected = [
            realpath(__DIR__.'/../../../fixtures/Functional/SimpleBundle/files/foo.yml'),
            __FILE__,
        ];

        $resolver = new KernelFileResolver($this->kernel);
        $actual = $resolver->resolve($files);

        $this->assertSame($expected, $actual);
    }

    public function testThrowsAnErrorIfTheFileResolvedByTheKernelIsNotAString()
    {
        if (Kernel::VERSION_ID >= 50000) {
            $this->markTestSkipped('Symfony 5 dropped the $first parameter in the locateResource method. This method can no longer return an array');
        }
        $this->expectException(\TypeError::class);

        $files = [
            '@SimpleBundle/files/foo.yml',
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->setLocateResourceFirst(false);
        $this->kernel->boot();

        $resolver = new KernelFileResolver($this->kernel);
        $resolver->resolve($files);
    }

    public function testThrowsAnExceptionIfFileResolvedByTheKernelDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $files = [
            '@SimpleBundle/dummy.yml',
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->boot();

        $resolver = new KernelFileResolver($this->kernel);
        $resolver->resolve($files);
    }

    public function testThrowsAnExceptionIfFileResolvedByTheKernelIsADirectory()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "@SimpleBundle/files" to be a fixture file, got a directory instead.');

        $files = [
            '@SimpleBundle/files',
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->boot();

        $resolver = new KernelFileResolver($this->kernel);
        $resolver->resolve($files);
    }

    public function testAbsoluteFilePathAreReturnedAndDuplicatesAreRemoved()
    {
        $files = [
            '@SimpleBundle/files/foo.yml',
            '@SimpleBundle/files/bar.yml',
            __FILE__,
            __DIR__.'/../File/KernelFileResolverTest.php',
        ];

        $this->kernel = new SimpleKernel('SimpleKernel_test', true);
        $this->kernel->boot();

        $expected = [
            realpath(__DIR__.'/../../../fixtures/Functional/SimpleBundle/files/foo.yml'),
            realpath(__DIR__.'/../../../fixtures/Functional/SimpleBundle/files/bar.yml'),
            __FILE__,
        ];

        $resolver = new KernelFileResolver($this->kernel);
        $actual = $resolver->resolve($files);

        $this->assertSame($expected, $actual);
    }
}
