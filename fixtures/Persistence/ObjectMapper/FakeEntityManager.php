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

namespace Hautelook\AliceBundle\Persistence\ObjectMapper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use function func_get_args;
use Hautelook\AliceBundle\NotCallableTrait;

class FakeEntityManager implements EntityManagerInterface
{
    use NotCallableTrait;

    public function getCache()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnection()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getExpressionBuilder()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function beginTransaction()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function transactional($func)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function commit()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function rollback()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createQuery($dql = '')
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createNamedQuery($name)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createNamedNativeQuery($name)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createQueryBuilder()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getReference($entityName, $id)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getPartialReference($entityName, $identifier)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function close()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function copy($entity, $deep = false)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getEventManager()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConfiguration()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function isOpen()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getUnitOfWork()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getHydrator($hydrationMode)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function newHydrator($hydrationMode)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getProxyFactory()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getFilters()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function isFiltersStateClean()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function hasFilters()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function find($className, $id)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function persist($object)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function remove($object)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function merge($object)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function clear($objectName = null)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function detach($object)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function refresh($object)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function flush()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getRepository($className)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getMetadataFactory()
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function initializeObject($obj)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function contains($object)
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getClassMetadata($className)
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
