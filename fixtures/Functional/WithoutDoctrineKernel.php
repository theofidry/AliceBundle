<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Functional;

use Hautelook\AliceBundle\HautelookAliceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Dennis Langen <langendennis81@gmail.com>
 */
class WithoutDoctrineKernel extends Kernel
{
    private $addedBundles = [];

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_without_doctrine.yml');
    }
}
