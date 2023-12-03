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

final class FixtureStore
{
    /**
     * @var string|null The name of the Doctrine manager to use
     */
    private static ?string $managerName = null;

    /**
     * @var string[] The list of bundles where to look for fixtures
     */
    private static array $bundles = [];

    /**
     * @var bool Append fixtures instead of purging
     */
    private static bool $append = false;

    /**
     * @var bool Use TRUNCATE to purge
     */
    private static bool $purgeWithTruncate = true;

    /**
     * @var string|null The name of the Doctrine connection to use
     */
    private static ?string $connectionName = null;

    /**
     * @var array|null Contain loaded fixture from alice
     */
    private static ?array $fixtures = null;

    public static function getFixtures(): ?array
    {
        return self::$fixtures;
    }

    public static function setFixtures(array $fixtures): void
    {
        self::$fixtures = $fixtures;
    }

    public static function getManagerName(): ?string
    {
        return self::$managerName;
    }

    public static function setManagerName(?string $managerName): void
    {
        self::$managerName = $managerName;
    }

    public static function getBundles(): array
    {
        return self::$bundles;
    }

    public static function setBundles(array $bundles): void
    {
        self::$bundles = $bundles;
    }

    public static function isAppend(): bool
    {
        return self::$append;
    }

    public static function setAppend(bool $append): void
    {
        self::$append = $append;
    }

    public static function isPurgeWithTruncate(): bool
    {
        return self::$purgeWithTruncate;
    }

    public static function setPurgeWithTruncate(bool $purgeWithTruncate): void
    {
        self::$purgeWithTruncate = $purgeWithTruncate;
    }

    public static function getConnectionName(): ?string
    {
        return self::$connectionName;
    }

    public static function setConnectionName(?string $connectionName): void
    {
        self::$connectionName = $connectionName;
    }

    private function __construct()
    {
    }
}
