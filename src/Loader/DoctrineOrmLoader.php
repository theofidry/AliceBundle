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
use Fidry\AliceDataFixtures\Bridge\Doctrine\Persister\ObjectManagerPersister;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PersisterAwareInterface;
use Fidry\AliceDataFixtures\Persistence\PersisterInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Hautelook\AliceBundle\BundleResolverInterface;
use Hautelook\AliceBundle\FixtureLocatorInterface;
use Hautelook\AliceBundle\LoaderInterface as AliceBundleLoaderInterface;
use Hautelook\AliceBundle\LoggerAwareInterface;
use InvalidArgumentException;
use LogicException;
use Nelmio\Alice\IsAServiceTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function sprintf;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class DoctrineOrmLoader implements AliceBundleLoaderInterface, LoggerAwareInterface
{
    use IsAServiceTrait;

    private BundleResolverInterface $bundleResolver;
    private FixtureLocatorInterface $fixtureLocator;
    /** @var LoaderInterface&PersisterAwareInterface */
    private LoaderInterface $purgeLoader;
    /** @var LoaderInterface&PersisterAwareInterface */
    private LoaderInterface $appendLoader;
    private LoggerInterface $logger;

    public function __construct(
        BundleResolverInterface $bundleResolver,
        FixtureLocatorInterface $fixtureLocator,
        LoaderInterface $purgeLoader,
        LoaderInterface $appendLoader,
        LoggerInterface $logger = null
    ) {
        $this->bundleResolver = $bundleResolver;
        $this->fixtureLocator = $fixtureLocator;

        if (false === $purgeLoader instanceof PersisterAwareInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected loader to be an instance of "%s".',
                    PersisterAwareInterface::class
                )
            );
        }

        if (false === $appendLoader instanceof PersisterAwareInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected loader to be an instance of "%s".',
                    PersisterAwareInterface::class
                )
            );
        }

        $this->purgeLoader = $purgeLoader;
        $this->appendLoader = $appendLoader;
        $this->logger = $logger ?? new NullLogger();
    }

    public function withLogger(LoggerInterface $logger): self
    {
        return new self(
            $this->bundleResolver,
            $this->fixtureLocator,
            $this->purgeLoader,
            $this->appendLoader,
            $logger
        );
    }

    public function load(
        Application $application,
        EntityManagerInterface $manager,
        array $bundles,
        string $environment,
        bool $append,
        bool $purgeWithTruncate,
        bool $noBundles = false
    ): array {
        if ($append && $purgeWithTruncate) {
            throw new LogicException(
                'Cannot append loaded fixtures and at the same time purge the database. Choose one.'
            );
        }

        if (!$noBundles) {
            $bundles = $this->bundleResolver->resolveBundles($application, $bundles);
        }

        $fixtureFiles = $this->fixtureLocator->locateFiles($bundles, $environment);

        $this->logger->info('fixtures found', ['files' => $fixtureFiles]);

        $purgeMode = $this->retrievePurgeMode($append, $purgeWithTruncate);

        $fixtures = $this->loadFixtures(
            $append ? $this->appendLoader : $this->purgeLoader,
            $manager,
            $fixtureFiles,
            $application->getKernel()->getContainer()->getParameterBag()->all(),
            $purgeMode
        );

        $this->logger->info('fixtures loaded');

        return $fixtures;
    }

    protected function createPersister(EntityManagerInterface $manager): PersisterInterface
    {
        return new ObjectManagerPersister($manager);
    }

    /**
     * @param LoaderInterface|PersisterAwareInterface $loader
     * @param string[]                                $files
     *
     * @return object[]
     */
    protected function loadFixtures(
        LoaderInterface $loader,
        EntityManagerInterface $manager,
        array $files,
        array $parameters,
        ?PurgeMode $purgeMode
    ): array {
        $persister = $this->createPersister($manager);

        $loader = $loader->withPersister($persister);

        return $loader->load($files, $parameters, [], $purgeMode);
    }

    private function retrievePurgeMode(bool $append, bool $purgeWithTruncate): ?PurgeMode
    {
        if ($append) {
            return null;
        }

        return (true === $purgeWithTruncate)
            ? PurgeMode::createTruncateMode()
            : PurgeMode::createDeleteMode()
        ;
    }
}
