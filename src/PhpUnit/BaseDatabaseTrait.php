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

use function count;
use function is_a;
use LogicException;
use function sprintf;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function trigger_deprecation;

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
     * @var string|null The name of the Doctrine connection to use
     */
    protected static ?string $connection = null;

    /**
     * @deprecated Use FixtureStore::getFixtures() instead.
     *
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
        $loader = $container->get('hautelook_alice.loader');

        self::populateFixtureStore();

        static::$fixtures = $loader->load(
            new Application(static::$kernel), // OK this is ugly... But there is no other way without redesigning LoaderInterface from the ground.
            $container->get('doctrine')->getManager(FixtureStore::getManagerName()),
            FixtureStore::getBundles(),
            static::$kernel->getEnvironment(),
            FixtureStore::isAppend(),
            FixtureStore::isPurgeWithTruncate(),
        );

        FixtureStore::setFixtures(static::$fixtures);
    }

    private static function populateFixtureStore(): void
    {
        if (null !== static::$manager) {
            self::triggerFixtureStoreDeprecation(
                'the database manager',
                'setManagerName',
            );

            FixtureStore::setManagerName(static::$manager);
        }

        if (count(static::$bundles) !== 0) {
            self::triggerFixtureStoreDeprecation(
                'the loaded bundles',
                'setBundles',
            );

            FixtureStore::setBundles(static::$bundles);
        }

        if (false !== static::$append) {
            self::triggerFixtureStoreDeprecation(
                'the append parameter',
                'setAppend',
            );

            FixtureStore::setAppend(static::$append);
        }

        if (true !== static::$purgeWithTruncate) {
            self::triggerFixtureStoreDeprecation(
                'the purge with truncate parameter',
                'setPurgeWithTruncate',
            );

            FixtureStore::setPurgeWithTruncate(static::$purgeWithTruncate);
        }

        if (null !== static::$connection) {
            self::triggerFixtureStoreDeprecation(
                'the connection name',
                'setConnectionName',
            );

            FixtureStore::setConnectionName(static::$connection);
        }
    }

    private static function triggerFixtureStoreDeprecation(
        string $subject,
        string $methodNameReplacement,
    ): void {
        trigger_deprecation(
            'hautelook/alice-bundle',
            '2.12.2',
            sprintf(
                'Setting up %s via the class static is deprecated. Use FixtureStore::%s() instead.',
                $subject,
                $methodNameReplacement,
            ),
        );
    }
}
