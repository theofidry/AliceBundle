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

namespace Hautelook\AliceBundle\Resolver;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class ResolverKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new ABundle(),
            new BBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        // Do nothing.
    }
}
