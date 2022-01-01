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

namespace Hautelook\AliceBundle\Alice\FileLocator;

use Nelmio\Alice\FileLocatorInterface;
use Nelmio\Alice\IsAServiceTrait;
use Symfony\Component\HttpKernel\KernelInterface;

final class KernelFileLocator implements FileLocatorInterface
{
    use IsAServiceTrait;

    private FileLocatorInterface $fileLocator;
    private KernelInterface $kernel;

    public function __construct(FileLocatorInterface $decoratedFileLocator, KernelInterface $kernel)
    {
        $this->fileLocator = $decoratedFileLocator;
        $this->kernel = $kernel;
    }

    public function locate(string $name, string $currentPath = null): string
    {
        if ('@' === $name[0]) {
            return $this->kernel->locateResource($name);
        }

        return $this->fileLocator->locate($name, $currentPath);
    }
}
