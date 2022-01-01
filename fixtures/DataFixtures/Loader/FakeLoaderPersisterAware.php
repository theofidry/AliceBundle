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

use Fidry\AliceDataFixtures\Persistence\PersisterAwareInterface;
use Fidry\AliceDataFixtures\Persistence\PersisterInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FakeLoaderPersisterAware extends FakeLoader implements PersisterAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function withPersister(PersisterInterface $persister)
    {
        $this->__call(__METHOD__, \func_get_args());
    }
}
