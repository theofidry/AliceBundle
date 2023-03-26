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

namespace Hautelook\AliceBundle;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

interface LoaderInterface
{
    /**
     * TODO: make `$noBundles = true` by default in 3.x.
     *  Accept the option nonetheless to ease the upgrade but add a deprecation notice
     *  to say this option is no longer necessary and later will be removed.
     *
     * Loads the specified fixtures of an application.
     *
     * @param Application            $application Application the fixtures are loaded from
     * @param EntityManagerInterface $manager     Entity Manager used for the loading
     * @param string[]               $bundles     Bundle names in which the fixtures can be found
     * @param string                 $environment If set filter the fixtures by the environment given
     * @param bool                   $append      If true, then the database is not purged before loading the objects
     * @param bool                   $noBundles   If true, then fixtures from other bundles will not be loaded
     *
     * @return object[] Loaded objects
     */
    public function load(
        Application $application,
        EntityManagerInterface $manager,
        array $bundles,
        string $environment,
        bool $append,
        bool $purgeWithTruncate,
        bool $noBundles = false
    ): array;
}
