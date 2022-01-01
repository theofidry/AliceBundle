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

namespace Hautelook\AliceBundle\Locator;

use function array_flip;
use function array_keys;
use Hautelook\AliceBundle\FixtureLocatorInterface;
use Nelmio\Alice\IsAServiceTrait;

final class EnvironmentlessFilesLocator implements FixtureLocatorInterface
{
    use IsAServiceTrait;

    private FixtureLocatorInterface $fixtureLocator;

    public function __construct(FixtureLocatorInterface $fixtureLocator)
    {
        $this->fixtureLocator = $fixtureLocator;
    }

    /**
     * Locate fixture files found matching the environment name.
     *
     * For example, if the given fixture path is 'Resources/fixtures', it will try to locate
     * the files in the 'Resources/fixtures/*.dev.yml' for each bundle passed ('dev' being the
     * environment).
     *
     * {@inheritdoc}
     */
    public function locateFiles(array $bundles, string $environment): array
    {
        $fixtureFiles = array_flip($this->fixtureLocator->locateFiles($bundles, $environment));
        $fixtureFiles = $fixtureFiles + array_flip($this->fixtureLocator->locateFiles($bundles, ''));

        return array_keys($fixtureFiles);
    }
}
