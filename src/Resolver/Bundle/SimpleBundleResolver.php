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

namespace Hautelook\AliceBundle\Resolver\Bundle;

use function array_values;
use Hautelook\AliceBundle\BundleResolverInterface;
use Hautelook\AliceBundle\Exception\Resolver\BundleNotFoundException;
use Nelmio\Alice\IsAServiceTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;

final class SimpleBundleResolver implements BundleResolverInterface
{
    use IsAServiceTrait;

    public function resolveBundles(Application $application, array $names): array
    {
        $bundles = $application->getKernel()->getBundles();

        $result = [];
        foreach ($names as $name) {
            if (false === isset($bundles[$name])) {
                throw BundleNotFoundException::create($name, $bundles);
            }

            $result[$name] = $bundles[$name];
        }

        return array_values($result);
    }
}
