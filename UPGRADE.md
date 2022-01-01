# Upgrading guide

## From 1.x to 2.x

1. Remove [DataLoaders](https://github.com/hautelook/AliceBundle/blob/1.x/src/Resources/doc/advanced-usage.md#using-data-loaders).
   Instead of having a data loader to reference a file in a different folder, you can create a "dummy" file, which will
   only contain an [include statement](https://github.com/nelmio/alice/blob/master/doc/fixtures-refactoring.md#including-files)
   and refer to the other file.
    
1. The persistence layer has been moved to [FidryAliceDataFixtures](https://github.com/theofidry/AliceDataFixtures). As
   a result, refer to this library documentation for configuration related to the database.
   
1. Alice now [ships with a Symfony bundle](https://github.com/nelmio/alice/blob/master/doc/getting-started.md#symfony) so
   some elements of the configuration have been moved from HautelookAliceBundle to NelmioAliceBundle.
   
1. The default and recommended location of fixtures have been changed from `src/AppBundle/DataFixtures{/ORM}` to
   `Resources/fixtures{/orm}` instead (from `app` or one of your bundle).

1. Faker provider are now registered with the tag `nelmio_alice.faker.provider` instead of `hautelook_alice.faker.provider`.

1. Processors are now registered with the tag `fidry_alice_data_fixtures.processor` instead of `hautelook_alice.alice.processor`


## From 0.2 to 1.x

### Upgrading the data loaders

1. You data loader should now either extend [`Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader`](https://github.com/theofidry/AliceBundle/tree/1.x/src/Doctrine/DataFixtures/AbstractLoader.php) or implement [`Hautelook\AliceBundle\Doctrine\DataFixtures\LoaderInterface`](https://github.com/theofidry/AliceBundle/tree/1.x/src/Doctrine/DataFixtures/LoaderInterface.php).

2. If you were overriding the `::load()` function of the data loader, you should not need it anymore now:
  * Custom Faker providers can now be registered, cf [Custom Faker Providers](https://github.com/theofidry/AliceBundle/tree/1.x/src/Resources/doc/faker-providers.md).
  * Custom Alice processors can now be registered, cf [Custom Processors](https://github.com/theofidry/AliceBundle/tree/1.x/src/Resources/doc/alice-processors.md).

3. If you had very long path for some fixtures because you needed to refer to the fixtures of another bundle, you can now use the bundle annotation `@Bundlename`.

4. If you had several data loaders to manage different set of fixtures depending of your environment, now you can [devide your fixtures by environment](https://github.com/theofidry/AliceBundle/tree/1.x/src/Resources/doc/advanced-usage.md#environment-specific-fixtures) instead of having to use and specify a data loader for that.


### Doctrine command

You should now rely on the bundle command `hautelook_alice:doctrine:fixtures:load` (or `h:d:f:l`) instead of `doctrine:fixtures:load`.


### Remove DoctrineFixturesBundle

As explained [here](https://github.com/theofidry/AliceBundle/tree/1.x/src/Resources/doc/doctrine-fixtures-bundle.md), there is no obligation to do so. HautelookAliceBundle is fully compatible with it. However it does not make sense to use the both of them together. It is recommended to
choose only one.

[Back to the documentation](README.md)
