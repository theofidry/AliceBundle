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

namespace Hautelook\AliceBundle\DependencyInjection;

use function array_keys;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Hautelook\AliceBundle\HautelookAliceBundle;
use function implode;
use LogicException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @private
 *
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class HautelookAliceExtension extends Extension
{
    private const SERVICES_DIR = __DIR__.'/../../resources/config';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $missingBundles = [
            DoctrineBundle::class => true,
            FidryAliceDataFixturesBundle::class => true,
        ];

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            unset($missingBundles[$bundle]);

            if (!$missingBundles) {
                break;
            }
        }

        if ($missingBundles) {
            throw new LogicException(
                sprintf(
                    'To register "%s", you also need: "%s".',
                    HautelookAliceBundle::class,
                    implode('", "', array_keys($missingBundles)),
                ),
            );
        }

        $this->loadConfig($configs, $container);
        $this->loadServices($container);
    }

    /**
     * Loads alice configuration and add the configuration values to the application parameters.
     */
    private function loadConfig(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $processedConfiguration = $this->processConfiguration($configuration, $configs);

        foreach ($processedConfiguration as $key => $value) {
            $container->setParameter(
                $this->getAlias().'.'.$key,
                $value
            );
        }
    }

    /**
     * Loads all the services declarations.
     */
    private function loadServices(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(self::SERVICES_DIR));
        $finder = new Finder();

        $finder->files()->in(self::SERVICES_DIR);

        foreach ($finder as $file) {
            $loader->load($file->getRelativePathname());
        }
    }
}
