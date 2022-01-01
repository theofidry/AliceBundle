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

namespace Hautelook\AliceBundle\Resolver;

use Hautelook\AliceBundle\BundleResolverInterface;
use Hautelook\AliceBundle\NotCallableTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FakeBundleResolver implements BundleResolverInterface
{
    use NotCallableTrait;

    /**
     * {@inheritdoc}
     */
    public function resolveBundles(Application $application, array $names)
    {
        $this->__call(__METHOD__, \func_get_args());
    }
}
