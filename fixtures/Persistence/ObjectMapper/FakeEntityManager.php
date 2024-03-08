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

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;g
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ObjectRepository;
use function func_get_args;
use Hautelook\AliceBundle\NotCallableTrait;

class FakeEntityManager implements EntityManagerInterface
{
    use NotCallableTrait;

    public function getCache(): ?Cache
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConnection(): Connection
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getExpressionBuilder(): Expr
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function beginTransaction(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function transactional($func): mixed
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function commit(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function rollback(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createQuery($dql = ''): Query
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createNamedQuery($name): Query
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm): NativeQuery
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createNamedNativeQuery($name): NativeQuery
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function createQueryBuilder(): QueryBuilder
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getReference($entityName, $id): ?object
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getPartialReference($entityName, $identifier): ?object
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function close(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function copy($entity, $deep = false): object
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function lock($entity, $lockMode, $lockVersion = null): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getEventManager(): EventManager
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getConfiguration(): Configuration
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function isOpen(): bool
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getUnitOfWork(): UnitOfWork
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getHydrator($hydrationMode): AbstractHydrator
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function newHydrator($hydrationMode): AbstractHydrator
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getProxyFactory(): ProxyFactory
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getFilters(): FilterCollection
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function isFiltersStateClean(): bool
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function hasFilters(): bool
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function find(string $className, $id): ?object
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function persist(object $object): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function remove(object $object): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function clear($objectName = null): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function detach(object $object): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function refresh(object $object): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function flush(): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getRepository($className): EntityRepository
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getMetadataFactory(): ObjectRepository
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function initializeObject(object $obj): void
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function contains(object $object): bool
    {
        $this->__call(__METHOD__, func_get_args());
    }

    public function getClassMetadata($className): ClassMetadata
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
