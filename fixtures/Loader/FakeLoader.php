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

namespace Hautelook\AliceBundle\Loader;

use Doctrine\ORM\EntityManagerInterface;
use function func_get_args;
use Hautelook\AliceBundle\LoaderInterface;
use Hautelook\AliceBundle\NotCallableTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class FakeLoader implements LoaderInterface
{
    use NotCallableTrait;

    public function load(
        Application $application,
        EntityManagerInterface $manager,
        array $bundles,
        string $environment,
        bool $append,
        bool $purgeWithTruncate,
        string $shard = null,
        bool $noBundles = false
    ): array {
        $this->__call(__METHOD__, func_get_args());
    }
}
