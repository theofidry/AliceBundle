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

use function array_map;
use function array_merge;
use Hautelook\AliceBundle\FixtureLocatorInterface;
use Nelmio\Alice\IsAServiceTrait;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class EnvDirectoryLocator implements FixtureLocatorInterface
{
    use IsAServiceTrait;

    private array $fixturePaths;
    private array $rootDirs;

    /**
     * @param string[] $fixturePaths paths in which to look for fixtures relative to the bundle/base directory paths
     * @param string[] $rootDirs     root directories
     */
    public function __construct(array $fixturePaths, array $rootDirs)
    {
        $this->fixturePaths = $fixturePaths;
        $this->rootDirs = $rootDirs;
    }

    /**
     * Locates fixture files found inside a folder matching the environment name.
     *
     * For example, if the given fixture path is 'Resources/fixtures', it will try to locate
     * the files in the 'Resources/fixtures/dev' for each bundle passed ('dev' being the
     * environment).
     *
     * {@inheritdoc}
     */
    public function locateFiles(array $bundles, string $environment): array
    {
        return array_merge(
            ...array_map(
                function (string $rootDir) use ($environment): array {
                    return $this->doLocateFiles($rootDir, $environment);
                },
                $this->rootDirs
            ),
            ...array_map(
                function (BundleInterface $bundle) use ($environment): array {
                    return $this->doLocateFiles($bundle->getPath(), $environment);
                },
                $bundles
            )
        );
    }

    /**
     * Gets the list of files that can be found in the given path.
     *
     * @return string[]
     */
    private function doLocateFiles(string $path, string $environment): array
    {
        $fullPaths = array_filter(array_map(static function (string $fixturePath) use ($environment, $path): string {
            return '' !== $environment
                ? sprintf('%s/%s/%s', $path, $fixturePath, $environment)
                : sprintf('%s/%s', $path, $fixturePath);
        }, $this->fixturePaths), function ($fullPath) {
            return $fullPath && file_exists($fullPath);
        });

        if ([] === $fullPaths) {
            return [];
        }

        $files = SymfonyFinder::create()->files()->in($fullPaths)->depth(0)->name('/.*\.(ya?ml|php|json)$/i');

        // this sort helps to set an order with filename
        // ( "001-root-level-fixtures.yml", "002-another-level-fixtures.yml", ... )
        $files = $files->sort(function ($a, $b) {
            return strcasecmp((string) $a, (string) $b);
        });

        $fixtureFiles = [];

        foreach ($files as $file) {
            $fixtureFiles[$file->getRealPath()] = true;
        }

        return array_keys($fixtureFiles);
    }
}
