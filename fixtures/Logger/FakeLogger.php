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

namespace Hautelook\AliceBundle\Logger;

use Hautelook\AliceBundle\NotCallableTrait;
use Psr\Log\LoggerInterface;

class FakeLogger implements LoggerInterface
{
    use NotCallableTrait;

    public function emergency($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function alert($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function critical($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function error($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function warning($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function notice($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function info($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function debug($message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    public function log($level, $message, array $context = []): void
    {
        $this->__call(__METHOD__, \func_get_args());
    }
}
