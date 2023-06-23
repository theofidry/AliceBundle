# Alice Processors

Given you declared a processor `App\DataFixtures\Processor\UserProcessor`, you have to declare it as a
service with the tag `fidry_alice_data_fixtures.processor` to register it unless `autoconfigure` is enabled:

```yaml
# config/services.yaml

services:
    _defaults:
        autoconfigure: true

    App\DataFixtures\Processor\UserProcessor: ~
```

Without `autoconfigure`:

```yaml
# config/services.yaml

services:
    App\DataFixtures\Processor\UserProcessor:
        tags: [ { name: fidry_alice_data_fixtures.processor } ]
```

Refer to [FidryAliceDataFixtures][alice-data-fixture-processors]
for a more advanced documentation.

Previous chapter: [Custom Faker providers](faker-providers.md)<br />
[Back to Table of Contents](../README.md#documentation)


[alice-data-fixture-processors]: https://github.com/theofidry/AliceDataFixtures/blob/master/doc/advanced-usage.md#processors
