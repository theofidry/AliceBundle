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

namespace Hautelook\AliceBundle\Alice\Generator\Instantiator\Chainable;

use function get_class;
use LogicException;
use Nelmio\Alice\Definition\MethodCall\MethodCallWithReference;
use Nelmio\Alice\Definition\Object\SimpleObject;
use Nelmio\Alice\Definition\ServiceReference\InstantiatedReference;
use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\Generator\GenerationContext;
use Nelmio\Alice\Generator\Instantiator\ChainableInstantiatorInterface;
use Nelmio\Alice\Generator\ResolvedFixtureSet;
use Nelmio\Alice\IsAServiceTrait;
use Nelmio\Alice\Throwable\Exception\Generator\Instantiator\InstantiationException;
use function sprintf;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class InstantiatedReferenceInstantiator implements ChainableInstantiatorInterface, ContainerAwareInterface
{
    use IsAServiceTrait;

    private ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function canInstantiate(FixtureInterface $fixture): bool
    {
        $constructor = $fixture->getSpecs()->getConstructor();

        return
            null !== $constructor
            && $constructor instanceof MethodCallWithReference
            && $constructor->getCaller() instanceof InstantiatedReference
        ;
    }

    public function instantiate(
        FixtureInterface $fixture,
        ResolvedFixtureSet $fixtureSet,
        GenerationContext $context
    ): ResolvedFixtureSet {
        $this->checkContainer(__METHOD__);
        $instance = $this->createInstance($fixture);

        return $this->generateSet($fixture, $fixtureSet, $instance);
    }

    private function checkContainer(string $method): void
    {
        if (null === $this->container) {
            throw new LogicException(
                sprintf(
                    'Expected instantiator method "%s" to be used only if it has a container, but no container could be found.',
                    $method,
                ),
            );
        }
    }

    private function createInstance(FixtureInterface $fixture): object
    {
        $constructor = $fixture->getSpecs()->getConstructor();

        if (null === $constructor) {
            throw new InstantiationException(
                sprintf(
                    'Expected fixture "%s" to have a constructor.',
                    $fixture->getId(),
                ),
            );
        }

        [$class, $factoryReference, $method, $arguments] = [
            $fixture->getClassName(),
            $constructor->getCaller()->getId(),
            $constructor->getMethod(),
            $constructor->getArguments(),
        ];

        if (null === $arguments) {
            $arguments = [];
        }

        $factory = $this->container->get($factoryReference);

        $instance = $factory->$method(...$arguments);

        if (!($instance instanceof $class)) {
            throw new InstantiationException(
                sprintf(
                    'Instantiated fixture was expected to be an instance of "%s". Got "%s" instead.',
                    $class,
                    get_class($instance)
                )
            );
        }

        return $instance;
    }

    private function generateSet(
        FixtureInterface $fixture,
        ResolvedFixtureSet $fixtureSet,
        $instance
    ): ResolvedFixtureSet {
        $objects = $fixtureSet->getObjects()->with(
            new SimpleObject(
                $fixture->getId(),
                $instance
            )
        );

        return $fixtureSet->withObjects($objects);
    }
}
