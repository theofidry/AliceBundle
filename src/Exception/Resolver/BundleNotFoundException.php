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

namespace Hautelook\AliceBundle\Exception\Resolver;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Throwable;

class BundleNotFoundException extends \RuntimeException
{
    /**
     * @param BundleInterface[] $bundles
     *
     * @return static
     */
    public static function create(string $bundle, array $bundles, int $code = 0, Throwable $previous = null)
    {
        return new static(
            sprintf(
                'The bundle "%s" was not found. Bundles available are: ["%s"].',
                $bundle,
                implode('", "', array_keys($bundles))
            ),
            $code,
            $previous
        );
    }
}
