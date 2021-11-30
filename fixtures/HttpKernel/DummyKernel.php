<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\HttpKernel;

use Hautelook\AliceBundle\NotCallableTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class DummyKernel implements KernelInterface
{
    use NotCallableTrait;

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MAIN_REQUEST, $catch = true): Response
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getBundles(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getBundle($name, $first = true): BundleInterface
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource($name, $dir = null, $first = true): string
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fake';
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment(): string
    {
        return 'fake_env';
    }

    /**
     * {@inheritdoc}
     */
    public function isDebug(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer(): ContainerInterface
    {
        return new Container();
    }

    /**
     * {@inheritdoc}
     */
    public function getStartTime(): float
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset(): string
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function isClassInActiveBundle($class)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectDir(): string
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function getBuildDir(): string
    {
        // TODO: Implement getBuildDir() method.
    }


}
