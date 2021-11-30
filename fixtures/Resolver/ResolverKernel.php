<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Resolver;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ResolverKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        return [
            new ABundle(),
            new BBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // Do nothing.
    }
}
