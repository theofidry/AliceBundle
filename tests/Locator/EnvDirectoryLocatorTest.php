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

use const DIRECTORY_SEPARATOR;
use Hautelook\AliceBundle\FixtureLocatorInterface;
use Hautelook\AliceBundle\Locator\EnvDirectoryLocator\AnotherDummyBundle\AnotherDummyBundle;
use Hautelook\AliceBundle\Locator\EnvDirectoryLocator\DummyBundle\DummyBundle;
use Hautelook\AliceBundle\Locator\EnvDirectoryLocator\EmptyBundle\EmptyBundle;
use Hautelook\AliceBundle\Locator\EnvDirectoryLocator\OneMoreDummyBundle\OneMoreDummyBundle;
use PHPUnit\Framework\TestCase;
use function realpath;
use ReflectionClass;

/**
 * @covers \Hautelook\AliceBundle\Locator\EnvDirectoryLocator
 */
class EnvDirectoryLocatorTest extends TestCase
{
    public function testIsAFixtureLocator(): void
    {
        self::assertTrue(is_a(EnvDirectoryLocator::class, FixtureLocatorInterface::class, true));
    }

    public function testIsNotClonable(): void
    {
        self::assertFalse((new ReflectionClass(EnvDirectoryLocator::class))->isCloneable());
    }

    /**
     * @dataProvider provideSets
     */
    public function testGetFilesFromABundle(array $bundles, string $environment, array $paths, array $expected): void
    {
        $invalidPath = '';
        $locator = new EnvDirectoryLocator($paths, [$invalidPath]);
        $actual = $locator->locateFiles($bundles, $environment);

        self::assertEqualsCanonicalizing($expected, $actual);
    }

    public function provideSets(): ?\Generator
    {
        yield 'bundle without any resources' => [
            [new EmptyBundle()],
            'test',
            ['Resources/fixtures'],
            [],
        ];

        yield 'bundle with non-existing path' => [
            [new EmptyBundle()],
            'test',
            ['ftp://Resources/fixtures'],
            [],
        ];

        yield 'bundle with file as path' => [
            [new EmptyBundle()],
            'test',
            ['Resources/fixtures/test/file1.yml'],
            [],
        ];

        yield 'bundle with fixture files' => [
            [new DummyBundle()],
            'test',
            ['Resources/fixtures'],
            [
                sprintf('%s/file1.yml', $prefix = realpath(__DIR__.'/../../fixtures/Locator/EnvDirectoryLocator/DummyBundle/Resources/fixtures/test')),
                $prefix.'/file2.yaml',
                $prefix.'/file3.php',
                $prefix.'/file5.YML',
                $prefix.'/file6.YAML',
            ],
        ];

        yield 'bundle with fixture files from multiple directories' => [
            [new DummyBundle()],
            'test',
            [
                'Resources/fixtures',
                'Resources/fixtures2',
            ],
            [
                sprintf('%s/file1.yml', realpath(__DIR__.'/../../fixtures/Locator/EnvDirectoryLocator/DummyBundle/Resources/fixtures2/test')),
                sprintf('%s/file1.yml', $prefix = realpath(__DIR__.'/../../fixtures/Locator/EnvDirectoryLocator/DummyBundle/Resources/fixtures/test')),
                $prefix.'/file2.yaml',
                $prefix.'/file3.php',
                $prefix.'/file5.YML',
                $prefix.'/file6.YAML',
            ],
        ];

        yield 'bundle  with fixture files but no fixtures in env' => [
            [new DummyBundle()],
            '',
            ['Resources/fixtures'],
            [],
        ];

        yield 'bundle with fixture files 2' => [
            [new DummyBundle()],
            '',
            ['Resources/fixtures/test'],
            [
                $prefix.'/file1.yml',
                $prefix.'/file2.yaml',
                $prefix.'/file3.php',
                $prefix.'/file5.YML',
                $prefix.'/file6.YAML',
            ],
        ];

        yield 'bundle with fixture files in a custom directory' => [
            [new AnotherDummyBundle()],
            'dev',
            ['resources'],
            [
                realpath(
                    __DIR__.'/../../fixtures/Locator/EnvDirectoryLocator/AnotherDummyBundle/resources/dev/file10.yaml'
                ),
            ],
        ];

        yield 'bundle with fixture directory but no fixture files' => [
            [new AnotherDummyBundle()],
            'test',
            ['resources'],
            [],
        ];
    }

    /**
     * @dataProvider provideSetsForSeveralBundles
     */
    public function testGetFilesFromSeveralBundles(
        array $bundles,
        string $environment,
        array $paths,
        array $expected
    ): void {
        $invalidPath = '';
        $locator = new EnvDirectoryLocator($paths, [$invalidPath]);
        $actual = $locator->locateFiles($bundles, $environment);

        self::assertEqualsCanonicalizing($expected, $actual);
    }

    public function provideSetsForSeveralBundles(): ?\Generator
    {
        $baseDir = 'Resources'.DIRECTORY_SEPARATOR.'fixtures';
        $baseDir2 = 'Resources'.DIRECTORY_SEPARATOR.'fixtures2';
        $env = 'test';

        $bundleA = new DummyBundle();
        $bundleB = new OneMoreDummyBundle();

        $prefixA1 = $bundleA->getPath().DIRECTORY_SEPARATOR.$baseDir.DIRECTORY_SEPARATOR.$env.DIRECTORY_SEPARATOR;
        $prefixA2 = $bundleA->getPath().DIRECTORY_SEPARATOR.$baseDir2.DIRECTORY_SEPARATOR.$env.DIRECTORY_SEPARATOR;
        $prefixB = $bundleB->getPath().DIRECTORY_SEPARATOR.$baseDir.DIRECTORY_SEPARATOR.$env.DIRECTORY_SEPARATOR;

        yield 'several bundles with fixture files' => [
            [$bundleA, $bundleB],
            $env,
            [$baseDir, $baseDir2],
            [
                $prefixA1.'file1.yml',
                $prefixA2.'file1.yml',
                $prefixA1.'file2.yaml',
                $prefixA1.'file3.php',
                $prefixA1.'file5.YML',
                $prefixA1.'file6.YAML',

                $prefixB.'file1.yml',
                $prefixB.'file2.yaml',
                $prefixB.'file3.php',
            ],
        ];
    }

    public function testGetFilesFromProjectDir(): void
    {
        $basePath = realpath(__DIR__.'/../..');

        $locator = new EnvDirectoryLocator(['fixtures/fixture_files'], [$basePath]);
        self::assertSame([$basePath.'/fixtures/fixture_files/city.yaml'], $locator->locateFiles([], ''));
    }
}
