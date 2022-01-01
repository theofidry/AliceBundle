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

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Hautelook\AliceBundle\HautelookAliceBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new NelmioAliceBundle(),
            new FidryAliceDataFixturesBundle(),
            new DoctrineBundle(),
            new HautelookAliceBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if ('public' !== $this->getEnvironment()) {
            $loader->load(__DIR__.'/config/config.yaml');
        } else {
            $loader->load(__DIR__.'/config/test/config.yaml');
        }

        $loader->load(__DIR__.'/config/doctrine.yaml');
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        if ('public' !== $this->getEnvironment()) {
            return;
        }

        $container->addCompilerPass(
            new MakeServicesPublicCompilerPass(),
            PassConfig::TYPE_OPTIMIZE,
        );
    }
}
