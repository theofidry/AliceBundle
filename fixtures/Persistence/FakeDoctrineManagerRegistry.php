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
use Doctrine\Persistence\ObjectManager;
use function func_get_args;
use Hautelook\AliceBundle\NotCallableTrait;

class FakeDoctrineManagerRegistry implements ManagerRegistry
{
    use NotCallableTrait;

    public function getDefaultConnectionName(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnection(?string $name = null): object
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnections(): array
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnectionNames(): array
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getDefaultManagerName(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManager(?string $name = null): ObjectManager
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagers(): array
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function resetManager(?string $name = null): ObjectManager
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagerNames(): array
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getRepository(string $persistentObject, ?string $persistentManagerName = null)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagerForClass(string $class): ?ObjectManager
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
