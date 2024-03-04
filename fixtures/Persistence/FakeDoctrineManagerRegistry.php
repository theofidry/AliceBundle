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

namespace Hautelook\AliceBundle\Persistence;

use Doctrine\Persistence\ManagerRegistry;
use function func_get_args;
use Hautelook\AliceBundle\NotCallableTrait;

class FakeDoctrineManagerRegistry implements ManagerRegistry
{
    use NotCallableTrait;

    public function getDefaultConnectionName(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnection($name = null): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnections(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnectionNames(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getDefaultManagerName(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManager($name = null): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagers(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function resetManager($name = null): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagerNames(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getRepository($persistentObject, $persistentManagerName = null): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagerForClass($class): void
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
