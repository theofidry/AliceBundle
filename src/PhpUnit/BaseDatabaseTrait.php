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

use function is_a;
use LogicException;
use function sprintf;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait BaseDatabaseTrait
{
    /**
     * @var string|null The name of the Doctrine manager to use
     */
    protected static ?string $manager = null;

    /**
     * @var string[] The list of bundles where to look for fixtures
     */
    protected static array $bundles = [];

    /**
     * @var bool Append fixtures instead of purging
     */
    protected static bool $append = false;

    /**
     * @var bool Use TRUNCATE to purge
     */
    protected static bool $purgeWithTruncate = true;

    /**
     * @var string|null The name of the Doctrine shard to use
     */
    protected static ?string $shard = null;

    /**
     * @var string|null The name of the Doctrine connection to use
     */
    protected static ?string $connection = null;

    /**
     * @var array|null Contain loaded fixture from alice
     */
    protected static ?array $fixtures = null;

    protected static function ensureKernelTestCase(): void
    {
        if (!is_a(static::class, KernelTestCase::class, true)) {
            throw new LogicException(sprintf('The test class must extend "%s" to use "%s".', KernelTestCase::class, static::class));
        }
    }

    protected static function populateDatabase(): void
    {
        $container = static::$kernel->getContainer();
        static::$fixtures = $container->get('hautelook_alice.loader')->load(
            new Application(static::$kernel), // OK this is ugly... But there is no other way without redesigning LoaderInterface from the ground.
            $container->get('doctrine')->getManager(static::$manager),
            static::$bundles,
            static::$kernel->getEnvironment(),
            static::$append,
            static::$purgeWithTruncate,
            static::$shard
        );
    }
}
