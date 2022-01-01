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

use Doctrine\ORM\EntityManager;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Brand;
use Hautelook\AliceBundle\Functional\TestKernel;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait RefreshTestTrait
{
    public function testRefresh(): void
    {
        $manager = $this->getManager();
        self::assertInitialState($manager);

        $newBrand = new Brand();
        $newBrand->setName('Les-Tilleuls.coop');
        $manager->persist($newBrand);
        $manager->flush();

        $brands = $manager->getRepository(Brand::class)->findAll();
        self::assertCount(11, $brands);
    }

    public function testShouldBeRefreshed(): void
    {
        self::assertInitialState($this->getManager());
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    private function getManager(): EntityManager
    {
        self::bootKernel();
        $container = self::$container ?? self::$kernel->getContainer();

        return $container->get('doctrine')->getManager();
    }

    private function assertInitialState(EntityManager $manager): void
    {
        $brands = $manager->getRepository(Brand::class)->findAll();
        self::assertCount(10, $brands);
    }
}
