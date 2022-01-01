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

namespace Hautelook\AliceBundle;

use Hautelook\AliceBundle\Exception\Resolver\BundleNotFoundException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

interface BundleResolverInterface
{
    /**
     * Looks at all the bundles registered in the application to return the bundles requested.
     *
     * @param Application $application Application in which bundles will be looked in
     * @param string[]    $names       Bundle names
     *
     * @throws BundleNotFoundException
     *
     * @return list<BundleInterface>
     */
    public function resolveBundles(Application $application, array $names): array;
}
