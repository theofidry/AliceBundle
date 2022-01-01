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
use Hautelook\AliceBundle\NotCallableTrait;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FakeDoctrineManagerRegistry implements ManagerRegistry
{
    use NotCallableTrait;

    /**
     * {@inheritdoc}
     */
    public function getDefaultConnectionName()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection($name = null)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getConnections()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionNames()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultManagerName()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getManager($name = null)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getManagers()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function resetManager($name = null)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasNamespace($alias)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerNames()
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        $this->__call(__METHOD__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerForClass($class)
    {
        $this->__call(__METHOD__, \func_get_args());
    }
}
