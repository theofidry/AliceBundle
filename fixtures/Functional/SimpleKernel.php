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

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class SimpleKernel extends Kernel
{
    /**
     * @var bool|null
     */
    private $overrideFirst;

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        return [
            new SimpleBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

    public function setLocateResourceFirst(bool $overrideFirst = null)
    {
        $this->overrideFirst = $overrideFirst;
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource($name, $dir = null, $first = true): string
    {
        if (null !== $this->overrideFirst) {
            $first = $this->overrideFirst;
        }

        if (Kernel::VERSION_ID >= 50000) {
            return parent::locateResource($name);
        }

        return parent::locateResource($name, $dir, $first);
    }
}
