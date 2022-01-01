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

namespace Hautelook\AliceBundle\HttpKernel;

use function func_get_args;
use Hautelook\AliceBundle\NotCallableTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class DummyKernel implements KernelInterface
{
    use NotCallableTrait;

    public function serialize()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function unserialize($serialized)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function handle(Request $request, $type = self::MAIN_REQUEST, $catch = true): Response
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function registerBundles(): iterable
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function boot(): void
    {
        // Do nothing
    }

    public function shutdown(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getBundles(): array
    {
        return [];
    }

    public function getBundle($name, $first = true): BundleInterface
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function locateResource($name, $dir = null, $first = true): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getName(): string
    {
        return 'fake';
    }

    public function getEnvironment(): string
    {
        return 'fake_env';
    }

    public function isDebug(): bool
    {
        return true;
    }

    public function getRootDir(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getContainer(): ContainerInterface
    {
        return new Container();
    }

    public function getStartTime(): float
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getCacheDir(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getLogDir(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getCharset(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getProjectDir(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getBuildDir(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
