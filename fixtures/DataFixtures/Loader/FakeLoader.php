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

namespace Hautelook\AliceBundle\DataFixtures\Loader;

use Fidry\AliceDataFixtures\LoaderInterface as DataFixturesLoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use function func_get_args;
use Hautelook\AliceBundle\NotCallableTrait;

class FakeLoader implements DataFixturesLoaderInterface
{
    use NotCallableTrait;

    public function load(array $fixturesFiles, array $parameters = [], array $objects = [], PurgeMode $purgeMode = null): array
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
