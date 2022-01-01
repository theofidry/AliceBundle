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
 * Purges and loads the fixtures before the first test and wraps all test in a transaction that will be roll backed when
 * it has finished.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait RefreshDatabaseTrait
{
    use BaseDatabaseTrait;

    protected static bool $dbPopulated = false;

    protected static function bootKernel(array $options = []): KernelInterface
    {
        static::ensureKernelTestCase();
        $kernel = parent::bootKernel($options);

        if (!static::$dbPopulated) {
            static::populateDatabase();
            static::$dbPopulated = true;
        }

        $container = static::$kernel->getContainer();
        $container->get('doctrine')->getConnection(static::$connection)->beginTransaction();

        return $kernel;
    }

    protected static function ensureKernelShutdown(): void
    {
        $container = null;
        if (null === $container && null !== static::$kernel) {
            $container = static::$kernel->getContainer();
        }

        if (null !== $container) {
            $connection = $container->get('doctrine')->getConnection(static::$connection);
            if ($connection->isTransactionActive()) {
                $connection->rollback();
            }
        }

        parent::ensureKernelShutdown();
    }
}
