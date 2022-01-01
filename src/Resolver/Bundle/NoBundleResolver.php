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

use Hautelook\AliceBundle\BundleResolverInterface;
use Nelmio\Alice\IsAServiceTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Decorates another resolver to return all bundles if no bundle requested.
 */
final class NoBundleResolver implements BundleResolverInterface
{
    use IsAServiceTrait;

    private BundleResolverInterface $decoratedResolver;

    public function __construct(BundleResolverInterface $decoratedResolver)
    {
        $this->decoratedResolver = $decoratedResolver;
    }

    public function resolveBundles(Application $application, array $names): array
    {
        if ([] === $names) {
            return array_values($application->getKernel()->getBundles());
        }

        return $this->decoratedResolver->resolveBundles($application, $names);
    }
}
