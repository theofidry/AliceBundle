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

use function array_keys;
use function implode;
use RuntimeException;
use function sprintf;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Throwable;

final class BundleNotFoundException extends RuntimeException
{
    /**
     * @param BundleInterface[] $bundles
     */
    public static function create(string $bundle, array $bundles, int $code = 0, Throwable $previous = null): self
    {
        return new self(
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
