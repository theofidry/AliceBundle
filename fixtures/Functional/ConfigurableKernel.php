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

use Hautelook\AliceBundle\HautelookAliceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class ConfigurableKernel extends Kernel
{
    /**
     * @var list<Bundle>
     */
    private array $addedBundles = [];

    public function registerBundles(): iterable
    {
        return array_merge(
            [
                new FrameworkBundle(),
                new HautelookAliceBundle(),
            ],
            $this->addedBundles
        );
    }

    public function addBundle(Bundle $bundle): self
    {
        $this->addedBundles[] = $bundle;

        return $this;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');
    }
}
