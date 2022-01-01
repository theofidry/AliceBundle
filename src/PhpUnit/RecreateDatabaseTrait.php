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

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Erase and recreate database schema before each tests. (All existing data will be lost!).
 *
 * @author Gary PEGEOT <garypegeot@gmail.com>
 */
trait RecreateDatabaseTrait
{
    use BaseDatabaseTrait;

    protected static function bootKernel(array $options = []): KernelInterface
    {
        static::ensureKernelTestCase();
        $kernel = parent::bootKernel($options);
        static::buildSchema();
        static::populateDatabase();

        return $kernel;
    }

    protected static function buildSchema(): void
    {
        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager(static::$manager);
        $meta = $em->getMetadataFactory()->getAllMetadata();

        if (!empty($meta)) {
            $tool = new SchemaTool($em);
            $tool->dropSchema($meta);
            try {
                $tool->createSchema($meta);
            } catch (ToolsException $e) {
                throw new \InvalidArgumentException("Database schema is not buildable: {$e->getMessage()}", $e->getCode(), $e);
            }
        }
    }
}
