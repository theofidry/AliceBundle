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

namespace Hautelook\AliceBundle\PhpUnit;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Purges and loads the fixtures before every tests.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait ReloadDatabaseTrait
{
    use BaseDatabaseTrait;

    protected static function bootKernel(array $options = []): KernelInterface
    {
        static::ensureKernelTestCase();
        $kernel = parent::bootKernel($options);
        static::populateDatabase();

        return $kernel;
    }
}
