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

    public function getConnection($name = null)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnections()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnectionNames()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getDefaultManagerName()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManager($name = null)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagers()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function resetManager($name = null)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getAliasNamespace($alias)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagerNames()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getManagerForClass($class)
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
