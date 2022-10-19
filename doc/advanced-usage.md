# Advanced usage

1. [Enabling databases](#enabling-databases)
1. [Environment specific fixtures](#environment-specific-fixtures)
1. [Fixtures parameters](#fixtures-parameters)
    1. [Alice parameters](#alice-parameters)
    1. [Application parameters](#application-parameters)
1. [Use service factories](#use-service-factories)
1. [Load fixtures in a specific order](#load-fixtures-in-a-specific-order)
    1. [Ordering the files found](#ordering-the-files-found)
    1. [Persisting the classes in a specific order](#persisting-the-classes-in-a-specific-order)


## Enabling databases

The database management is done in [FidryAliceDataFixtures](https://github.com/theofidry/AliceDataFixtures). Head
to this library documentation for a complete reference.


## Environment specific fixtures

If you wish to use different fixtures depending of the environment, you can easily organise your fixtures the following way:

```.
└── fixtures
    ├── environmentless-fixture1.yml
    ├── ...
    ├── dev
    │   ├── dev-fixture1.yml
    │   └── ...
    └── inte
        ├── prod-fixture1.yml
        └── ...
```

Then, when you're running the command `php app/console hautelook:fixtures:load --env=inte`, it will load all the
fixtures found in `Resources/fixtures/` (i.e.`environmentless-fixture1.yml`) and in `Resources/fixtures/inte`.


## Fixtures parameters

### Alice parameters

You can already use parameters specifics to your fixture file with
[Alice](https://github.com/nelmio/alice/blob/master/doc/fixtures-refactoring.md#parameters). To manage your fixtures
parameters, you may wish to have a dedicated file for that:

```yaml
# fixtures/parameters.yaml

parameters:
    app.alice.parameters.parameter1: something
    app.alice.parameters.parameter2: something else
    ...
```

Then you can use the parameters `app.alice.parameters.parameter1` across all your fixtures files:

```yaml
# fixtures/dummy.yaml

AppBundle\Entity\Dummy:
    dummy_0:
        name: <{app.alice.parameters.parameter1}>
```

You can also pass your parameters to functions:

```yaml
# fixtures/dummy.yaml

AppBundle\Entity\Dummy:
    dummy_0:
        name: <foo(<{app.alice.parameters.parameter1}>)>
```

For more, check [Alice documentation](https://github.com/nelmio/alice#table-of-contents).


### Application parameters

You can access out of the box to your application parameters:

```yaml
# fixtures/dummy.yaml

AppBundle\Entity\Dummy:
    dummy_0:
        locale: <{framework.validation.enabled}>
```

Alice parameters will **not** be injected in your application `ParameterBag`, i.e. are not re-usable outside of the
fixtures.


## Use service factories

If your entity `AppBundle\Entity\Dummy` requires a factory registered as a service (Alice already supports [static
factories](https://github.com/nelmio/alice/blob/master/doc/complete-reference.md#specifying-constructor-arguments)) to
`dummy_factory` be instantiated, you can specify it as a constructor:

```yaml
# fixtures/dummy.yaml

AppBundle\Entity\Dummy:
    dummy_0:
        __factory: { '@dummy_factory::create': ['<username()>'] }
```


## Load fixtures in a specific order

Thanks to the new design of alice, you should not have to worry about the order as circular references are now handled
correctly. If for some reasons you still need it, please proceed.


### Ordering the files found

You can do so by sorting the files found which are going to be loader by creating a custom file locator:

```php
<?php declare(strict_types=1);

namespace Acme\Alice\Locator;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use Nelmio\Alice\IsAServiceTrait;

final class CustomOrderFilesLocator implements FixtureLocatorInterface
{
    use IsAServiceTrait;

    private $decoratedFixtureLocator;

    public function __construct(FixtureLocatorInterface $decoratedFixtureLocator)
    {
        $this->decoratedFixtureLocator = $decoratedFixtureLocator;
    }

    /**
     * Re-order the files found by the decorated finder.
     *
     * {@inheritdoc}
     */
    public function locateFiles(array $bundles, string $environment): array
    {
        $files = $this->decoratedFixtureLocator->locateFiles($bundles, $environment);

        // TODO: order the files found in whatever order you want

        // Warning: the order will only affect how the fixture definitions are merged. Indeed the order in which they
        // are instantiated afterwards by nelmio/alice may change due to handling the fixture dependencies and
        // circular references.

        return $files;
    }
}
```

You then need to register your file locator:

```yaml
// config/services.yaml

services:
    Acme\Alice\Locator\CustomOrderFilesLocator:
        arguments:
            - '@hautelook_alice.locator.environmentless'    # Decorates the currently used file locator

    # Replaces the default file locator used
    hautelook_alice.locator: '@Acme\Alice\Locator\CustomOrderFilesLocator'
```

Keep in mind however than:

- when loaded, the files are actually merged together
- the fixtures are not necessarily loaded in the same order as their declaration since there might be dependencies
  to take into consideration


### Persisting the classes in a specific order

Maybe more likely to be relevant than the previous example. For doing so, you should register you own persister loader:

```php
<?php declare(strict_types=1);

namespace Acme\Alice\Loader;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Nelmio\Alice\IsAServiceTrait;

/**
 * @final
 */
/*final*/ class CustomOrderLoader implements LoaderInterface
{
    use IsAServiceTrait;

    private $decoratedLoader;

    public function __construct(LoaderInterface $decoratedLoader)
    {
        $this->decoratedLoader = $decoratedLoader;
    }
    /**
     * Pre process, persist and post process each object loaded.
     *
     * {@inheritdoc}
     */
    public function load(array $fixturesFiles, array $parameters = [], array $objects = [], PurgeMode $purgeMode = null): array
    {
        // We get the objects from the decorated loader
        $objects = $this->decoratedLoader->load($fixturesFiles, $parameters, $objects, $purgeMode);

        // TODO: re-order the objects we want them to be persisted

        return $objects;
    }
}
```

For it to work, we then need to decorate the loader getting the objects from the files but before they are persisted:

```yaml
// config/services.yaml

services:
    Acme\Alice\Loader\CustomOrderLoader:
        decorates: 'fidry_alice_data_fixtures.loader.simple'
        arguments:
            - '@Acme\Alice\Loader\CustomOrderLoader.inner'
```

Done.


## Validation

By default, fixtures are not validated. If you want to trigger the validation you
can do it with [a custom processor](alice-processors.md):

```php
<?php declare(strict_types=1);

namespace App\DataFixtures\Processor;

use DomainException;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ArticleProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    public function preProcess(string $id, object $object): void
    {
        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($object);
        if ($violations->count() > 0) {
            $message = sprintf("Error when validating fixture %s, violation(s) detected:\n%s", $id, $violations);
            throw new DomainException($message);
        }
    }

    public function postProcess(string $id, object $object): void
    {
    }
}
```

You can find more explanations in this [blog post](https://www.strangebuzz.com/en/blog/validating-your-data-fixtures-with-the-alice-symfony-bundle).


Previous chapter: [Basic usage](../README.md#basic-usage)<br />
Next chapter: [Custom Faker Providers](faker-providers.md)
