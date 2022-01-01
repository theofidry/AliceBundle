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

namespace Hautelook\AliceBundle\Console\Command\Doctrine;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\DBAL\Sharding\PoolingShardConnection;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\Functional\AppKernel;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Brand;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Product;
use Hautelook\AliceBundle\Functional\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversNothing
 * @group legacy
 */
class LoadDataFixturesCommandIntegrationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var AppKernel
     */
    private $kernel;

    /**
     * @var DoctrineOrmLoadDataFixturesCommand
     */
    private $command;

    /**
     * @var EntityManagerInterface
     */
    private $defaultEntityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        if (false === class_exists(DoctrineBundle::class, true)) {
            $this->markTestSkipped('DoctrineBundle is not installed.');
        }

        $this->kernel = new TestKernel('LoadDataFixturesCommandIntegrationTest', true);
        $this->kernel->boot();
        $this->application = new Application($this->kernel);
        $this->application->setAutoExit(false);

        $this->command = $this->kernel->getContainer()->get('hautelook_alice.console.command.doctrine.doctrine_orm_load_data_fixtures_command');

        $doctrine = $this->kernel->getContainer()->get('doctrine');
        $this->defaultEntityManager = $doctrine->getManager();

        // Create required MySQL databases for fixtures
        $this->runConsole('doctrine:database:create', ['--if-not-exists' => true, '--connection' => 'default']);
        $this->runConsole(
            'doctrine:database:create',
            ['--if-not-exists' => true, '--connection' => 'default', '--shard' => 1]
        );

        // Reset fixtures schemas
        foreach ($doctrine->getManagers() as $name => $manager) {
            $this->runConsole('doctrine:schema:drop', ['--force' => true, '--em' => $name]);
            $this->runConsole('doctrine:schema:create', ['--em' => $name]);
            $connection = $manager->getConnection();

            if ($connection instanceof PoolingShardConnection) {
                $connection->connect(1);
                $this->runConsole('doctrine:schema:drop', ['--force' => true, '--em' => $name]);
                $this->runConsole('doctrine:schema:create', ['--em' => $name]);
                $connection->connect(0);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->kernel->shutdown();
    }

    public function testFixturesLoading()
    {
        $command = $this->application->find('hautelook:fixtures:load');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => 'hautelook:fixtures:load',
            ],
            [
                'interactive' => false,
            ]
        );

        $this->verifyProducts(20);
        $this->verifyBrands(10);
    }

    public function testAppendFixtures()
    {
        $command = $this->application->find('hautelook:fixtures:load');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => 'hautelook:fixtures:load',
            ],
            [
                'interactive' => false,
            ]
        );
        $commandTester->execute(
            [
                'command' => 'hautelook:fixtures:load',
                '-e' => 'Inte',
                '--append' => null,
            ],
            [
                'interactive' => false,
            ]
        );

        $this->verifyProducts(40);
        $this->verifyBrands(20);
    }

    /**
     * @dataProvider loadCommandProvider
     */
    public function testFixturesRegisteringUsingInvalidManager(array $inputs, string $expected)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Doctrine (fixtures|ORM) Manager named "foo" does not exist\.$/');

        $command = $this->application->find('hautelook:fixtures:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                [
                    'command' => 'hautelook:fixtures:load',
                    '--manager' => 'foo',
                ],
                $inputs
            ),
            ['interactive' => false]
        );

        $this->assertFixturesDisplayEquals($expected, $commandTester->getDisplay());
    }

    /**
     * @dataProvider loadCommandProvider
     */
    public function testFixturesRegisteringUsingMySQL(array $inputs, string $expected)
    {
        $command = $this->application->find('hautelook:fixtures:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(
                [
                    'command' => 'hautelook:fixtures:load',
                    '--manager' => 'default',
                ],
                $inputs
            ),
            ['interactive' => false]
        );

        $this->assertFixturesDisplayEquals($expected, $commandTester->getDisplay());
    }

    public function loadCommandProvider()
    {
        $data = [];

        $data[] = [
            [],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'dev',
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Dev/dev.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'Prod',
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Prod/prod.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'prod',
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Prod/prod.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'dev',
                '--bundle' => [
                    'TestBundle',
                ],
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Dev/dev.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'dev',
                '--bundle' => [
                    'TestABundle',
                ],
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'dev',
                '--bundle' => [
                    'TestBundle',
                    'TestABundle',
                ],
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Dev/dev.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'dev',
                '--bundle' => [
                    'TestCBundle',
                ],
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'ignored',
                '--bundle' => [
                    'TestBundle',
                ],
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'ignored2',
                '--bundle' => [
                    'TestBundle',
                ],
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Ignored2/notIgnored.yml
  > purging database
  > fixtures loaded
EOF
        ];

        $data[] = [
            [
                '--env' => 'provider',
                '--bundle' => [
                    'TestBundle',
                ],
            ],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Provider/testFormatter.yml
  > purging database
  > fixtures loaded
EOF
        ];

        // Fix paths
        foreach ($data as $index => $dataSet) {
            $data[$index][1] = str_replace('/home/travis/build/theofidry/AliceBundle', getcwd(), $dataSet[1]);
        }

        return $data;
    }

    /**
     * @param string $expected
     * @param string $display
     */
    protected function assertFixturesDisplayEquals($expected, $display)
    {
        $expected = $this->normalizeFixturesDisplay($expected);
        $display = $this->normalizeFixturesDisplay($display);
        $this->assertCount(0, array_diff($expected, $display));
    }

    /**
     * @param string $display
     *
     * @return string[]
     */
    private function normalizeFixturesDisplay($display)
    {
        $display = trim($display, ' ');
        $display = trim($display, "\t");
        $display = preg_replace('/\n/', '', $display);
        $display = explode('  > loading ', $display);
        array_shift($display);

        return $display;
    }

    private function runConsole(string $command, array $options = []): int
    {
        $options['-e'] = 'test';
        $options['-q'] = null;
        $options = array_merge($options, ['command' => $command]);

        return $this->application->run(new ArrayInput($options));
    }

    private function verifyProducts(int $count)
    {
        $this->assertCount(
            $count,
            $this->defaultEntityManager->getRepository(Product::class)->findAll()
        );

        for ($i = 1; $i <= $count; ++$i) {
            /* @var Product|null $product */
            $product = $this->defaultEntityManager->find(Product::class, $i);
            $this->assertNotNull($product);
            $this->assertStringStartsWith('Awesome Product', $product->getDescription());
            // Make sure every product has a brand
            $this->assertInstanceOf(
                Brand::class,
                $product->getBrand()
            );
        }
    }

    private function verifyBrands(int $count)
    {
        $this->assertCount(
            $count,
            $this->defaultEntityManager->getRepository(Brand::class)->findAll()
        );

        for ($i = 1; $i <= $count; ++$i) {
            /* @var Brand|null $brand */
            $brand = $this->defaultEntityManager->find(Brand::class, $i);
            $this->assertNotNull($brand);
        }
    }
}
