AliceBundle
===========

A [Symfony](http://symfony.com) bundle to manage fixtures with [nelmio/alice](https://github.com/nelmio/alice) and
[fzaninotto/Faker](https://github.com/fzaninotto/Faker).

The database support is done in [FidryAliceDataFixtures](https://github.com/theofidry/AliceDataFixtures). Check this
project to know which database/ORM is supported.

**Warning: this is the documentation for HautelookAliceBundle 2.0. If you want to check the documentation for 1.x, head
[this way](https://github.com/theofidry/AliceBundle/tree/1.x).**

[![Package version](https://img.shields.io/packagist/v/hautelook/alice-bundle.svg?style=flat-square)](https://packagist.org/packages/hautelook/alice-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/theofidry/AliceBundle/CI.svg?style=flat-square)](https://github.com/theofidry/AliceBundle/actions)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/d93a3fc4-3fe8-4be3-aa62-307f53898199.svg?style=flat-square)](https://insight.sensiolabs.com/projects/d93a3fc4-3fe8-4be3-aa62-307f53898199)
[![Dependency Status](https://www.versioneye.com/user/projects/55d26478265ff6001a000084/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d26478265ff6001a000084)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/hautelook/AliceBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/hautelook/AliceBundle/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hautelook/AliceBundle.svg?b=master&style=flat-square)](https://scrutinizer-ci.com/g/hautelook/AliceBundle/?branch=master)
[![Slack](https://img.shields.io/badge/slack-%23alice--fixtures-red.svg?style=flat-square)](https://symfony-devs.slack.com/shared_invite/MTYxMjcxMjc0MTc5LTE0OTA3ODE4OTQtYzc4NWVmMzRmZQ)


## When to use this bundle?

HautelookAliceBundle changed a lot, it first was acting as a simple bundle for [nelmio/alice](https://github.com/nelmio/alice),
it then started to ship some additional features to enrich it.

HautelookAliceBundle 1.x was the first milestone reaching a certain level of maturity in its usage:

- Easily load a set of fixtures from a command
- Being able to define different sets of fixtures for multiple environments
- Customize the data generation with custom Faker providers
- Customize the loading behaviour with processors

HautelookAliceBundle 2.x changes a lot, although not so much. In 1.x, a lot of complexity was brought in the bundle
due to nelmio/alice 2.x limitations and were at best workarounds (like the lack of handling of circular references).
A lot of that complexity has been pushed back to nelmio/alice 3.x which has a much more flexible design. As a result:

- [nelmio/alice](https://github.com/nelmio/alice) 3.x allows you to easily create PHP objects with random data in an elegant way
- [FidryAliceDataFixtures](https://github.com/theofidry/AliceDataFixtures) is a persistence layer for nelmio/alice 3.x. If you need to persist the loaded objects,
  it is the package you need. It provides you the flexibility to be able to purge the data between each loadings or
  wrap the loading in a transaction for your tests for example to simply rollback once the test is finished instead of
  calling an expansive purge.
- hautelook/alice-bundle 2.x provides high-level features including fixtures discovery (find the appropriate files and load them),
  and helpers for database testing.
  If you just need to load specific sets of files for your tests, [FidryAliceDataFixtures](https://github.com/theofidry/AliceDataFixtures) is enough.


## Documentation

1. [Install](#installation)
1. [Basic usage](#basic-usage)
1. [Database testing](#database-testing)
1. [Advanced usage](doc/advanced-usage.md)
    1. [Enabling databases](doc/advanced-usage.md#enabling-databases)
    1. [Environment specific fixtures](doc/advanced-usage.md#environment-specific-fixtures)
    1. [Fixtures parameters](doc/advanced-usage.md#fixtures-parameters)
        1. [Alice parameters](doc/advanced-usage.md#alice-parameters)
        1. [Application parameters](doc/advanced-usage.md#application-parameters)
    1. [Use service factories](doc/advanced-usage.md#use-service-factories)
    1. [Load fixtures in a specific order](doc/advanced-usage.md#load-fixtures-in-a-specific-order)
        1. [Load fixtures in a specific order](doc/advanced-usage.md#ordering-the-files-found)
        1. [Persisting the classes in a specific order](doc/advanced-usage.md#persisting-the-classes-in-a-specific-order)
   1. [Validation](doc/advanced-usage.md#validation)
1. [Custom Faker Providers](doc/faker-providers.md)
1. [Custom Alice Processors](doc/alice-processors.md)
1. [Resources](#resources)


## Installation

With [Symfony Flex](https://symfony.com/doc/current/setup/flex.html) (recommended):

```
# If you do not have Doctrine installed yet:
composer require doctrine-orm

composer require --dev hautelook/alice-bundle
```

You're ready to use AliceBundle, and can jump to the next section!

Without Flex you will have to install `doctrine/orm` and register the bundles accordingly in `app/AppKernel.php` or
wherever your Kernel class is located:

```php
<?php
// app/AppKernel.php

public function registerBundles(): iterable
{
    $bundles = [
        new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
        // ...
        new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    ];

    if (in_array($this->getEnvironment(), ['dev', 'test'])) {
        //...
        $bundles[] = new Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle();
        $bundles[] = new Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle();
        $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
    }

    return $bundles;
}
```

Configure the bundle to your needs, for example:

```yaml
# config/packages/dev/hautelook_alice.yaml

hautelook_alice:
    fixtures_path: 'fixtures' # Path to which to look for fixtures relative to the project directory or the bundle path. May be a string or an array of strings.
    root_dirs:
        - '%kernel.root_dir%'
        - '%kernel.project_dir%'
```

If you are using a non-flex architecture, you may want to use `Resources/fixtures` instead of `fixtures`.


## Basic usage

Assuming you are using [Doctrine](http://www.doctrine-project.org/projects/orm.html), make sure you
have the [`doctrine/doctrine-bundle`](https://github.com/doctrine/DoctrineBundle) and
[`doctrine/data-fixtures`](https://github.com/doctrine/data-fixtures) packages installed.

Then create a fixture file in one of the following location:

- `fixtures` if you are using flex
- `app/Resources/fixtures` if you have a non-flex bundle-less Symfony application
- `src/AppBundle/Resources/fixtures` or any bundle under which you want to place the fixtures

```yaml
# fixtures/dummy.yaml

App\Entity\Dummy:
    dummy_{1..10}:
        name: <name()>
        related_dummy: '@related_dummy*'
```

```yaml
# fixtures/related_dummy.yaml

App\Entity\RelatedDummy:
    related_dummy_{1..10}:
        name: <name()>
```

Then simply load your fixtures with the doctrine command `php bin/console hautelook:fixtures:load`.

If you want to load the fixtures of a bundle only, do `php bin/console hautelook:fixtures:load -b MyFirstBundle -b MySecondBundle`.

[See more](#documentation).<br />
Next chapter: [Advanced usage](doc/advanced-usage.md)


## Database testing

The bundle provides nice helpers, [inspired by Laravel](https://laravel.com/docs/5.6/database-testing#resetting-the-database-after-each-test),
dedicated for database testing: `RefreshDatabaseTrait`, `ReloadDatabaseTrait` and `RecreateDatabaseTrait`.
These traits allow to easily reset the database in a known state before each PHPUnit test: it purges the database then loads
the fixtures.

They are particularly helpful when writing [functional tests](https://symfony.com/doc/current/testing.html#functional-tests)
and when using [Panther](https://github.com/symfony/panther).

To improve performance, `RefreshDatabaseTrait` populates the database only one time, then wraps every tests in a
transaction that will be rolled back at the end after its execution (regardless of if it's a success or a failure):

```php
<?php

namespace App\Tests;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function postCommentTest()
    {
        $client = static::createClient(); // The transaction starts just after the boot of the Symfony kernel
        $crawler = $client->request('GET', '/my-news');
        $form = $crawler->filter('#post-comment')->form(['new-comment' => 'Symfony is so cool!']);
        $client->submit($form);
        // At the end of this test, the transaction will be rolled back (even if the test fails)
    }
}
```

Sometimes, wrapping tests in transactions is not possible. For instance, when using Panther, changes to the database
are made by another PHP process, so it wont work.
In such cases, use the `ReloadDatabase` trait. It will purge the DB and load fixtures before every tests:

```php
<?php

namespace App\Tests;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\Panther\PantherTestCase;

class NewsTest extends PantherTestCase // Be sure to extends KernelTestCase, WebTestCase or PantherTestCase
{
    use ReloadDatabaseTrait;

    public function postCommentTest()
    {
        $client = static::createPantherClient();// The database will be reset after every boot of the Symfony kernel

        $crawler = $client->request('GET', '/my-news');
        $form = $crawler->filter('#post-comment')->form(['new-comment' => 'Symfony is so cool!']);
        $client->submit($form);
    }
}
```

This strategy doesn't work when using Panther, because the changes to the database are done by another process, outside
of the transaction.

Both traits provide several configuration options as protected static properties:

* `self::$manager`: The name of the Doctrine manager to use
* `self::$bundles`: The list of bundles where to look for fixtures
* `self::$append`: Append fixtures instead of purging
* `self::$purgeWithTruncate`: Use TRUNCATE to purge
* `self::$connection`: The name of the Doctrine connection to use

Use them in the `setUpBeforeClass` method.

```php
<?php

namespace App\Tests;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public static function setUpBeforeClass()
    {
        self::$append = true;
    }

    // ...
}
```

Finally, if you're using in memory SQLite for your tests, use `RecreateDatabaseTrait` to create the database schema "on the fly":
```php
<?php

namespace App\Tests;

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    // ...
}
```

## Resources

* Behat extension: [AliceBundleExtension](https://github.com/theofidry/AliceBundleExtension)
* Bundle for generating AliceBundle compatible fixtures directly from Doctrine entities: [AliceGeneratorBundle](https://github.com/trappar/AliceGeneratorBundle)
* [Upgrade guide](UPGRADE.md)
  * [Upgrade from 0.X to 1.X](UPGRADE.md#from-0x-to-1x)
* [Changelog](CHANGELOG.md)


## Credits

This bundle was originaly developped by [Baldur RENSCH](https://github.com/baldurrensch) and [HauteLook](https://github.com/hautelook). It is now maintained by [Théo FIDRY](https://github.com/theofidry).

[Other contributors](https://github.com/theofidry/AliceBundle/graphs/contributors).


## License

[![license](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)
