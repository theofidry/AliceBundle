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

final class RefreshDatabaseState
{
    private static bool $dbPopulated = false;

    public static function setDbPopulated(bool $populated): void
    {
        self::$dbPopulated = $populated;
    }

    public static function isDbPopulated(): bool
    {
        return self::$dbPopulated;
    }

    private function __construct()
    {
    }
}
