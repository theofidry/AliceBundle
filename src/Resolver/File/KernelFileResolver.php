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

use function array_keys;
use Fidry\AliceDataFixtures\FileResolverInterface;
use function file_exists;
use InvalidArgumentException;
use function is_file;
use Nelmio\Alice\IsAServiceTrait;
use function realpath;
use function sprintf;
use Symfony\Component\HttpKernel\KernelInterface;

final class KernelFileResolver implements FileResolverInterface
{
    use IsAServiceTrait;

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function resolve(array $filePaths): array
    {
        $resolvedFixtures = [];
        foreach ($filePaths as $file) {
            $resolvedFixtures[$this->resolveFile($file)] = true;
        }

        return array_keys($resolvedFixtures);
    }

    public function resolveFile(string $file): string
    {
        $realFile = ('@' === $file[0])
            ? $this->kernel->locateResource($file)
            : $file
        ;

        $realFile = realpath($realFile);
        if (false === $realFile || false === file_exists($realFile)) {
            throw new InvalidArgumentException(sprintf('The file "%s" was not found.', $file));
        }
        if (false === is_file($realFile)) {
            throw new InvalidArgumentException(
                sprintf('Expected "%s" to be a fixture file, got a directory instead.', $file)
            );
        }

        return $realFile;
    }
}
