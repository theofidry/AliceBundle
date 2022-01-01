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

namespace Hautelook\AliceBundle\Functional;

use Hautelook\AliceBundle\Functional\SimpleBundle\SimpleBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class SimpleKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new SimpleBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        // Nothing to do
    }
}
