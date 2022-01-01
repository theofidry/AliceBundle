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

namespace Hautelook\AliceBundle\FixtureLocator;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use Hautelook\AliceBundle\NotCallableTrait;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FakeFixtureLocator implements FixtureLocatorInterface
{
    use NotCallableTrait;

    /**
     * {@inheritdoc}
     */
    public function locateFiles(array $bundles, string $environment): array
    {
        $this->__call(__METHOD__, \func_get_args());
    }
}
