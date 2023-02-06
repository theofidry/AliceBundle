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

use function array_diff;
use function array_merge;
use function array_shift;
use Doctrine\ORM\EntityManagerInterface;
use function explode;
use function getcwd;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Brand;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Product;
use Hautelook\AliceBundle\Functional\TestKernel;
use PHPUnit\Framework\TestCase;
use function preg_replace;
use function str_replace;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use function trim;

/**
 * @coversNothing
 *
 * @group legacy
 */
class LoadDataFixturesCommandIntegrationTest extends TestCase
{
    private Application $application;

    private KernelInterface $kernel;

    private EntityManagerInterface $defaultEntityManager;

    protected function setUp(): void
    {
        $this->kernel = new TestKernel('LoadDataFixturesCommandIntegrationTest', true);
        $this->kernel->boot();
        $this->application = new Application($this->kernel);
        $this->application->setAutoExit(false);

        $doctrine = $this->kernel->getContainer()->get('doctrine');
        $this->defaultEntityManager = $doctrine->getManager();

        // Create required MySQL databases for fixtures
        $this->runConsole('doctrine:database:create', ['--if-not-exists' => true, '--connection' => 'default']);
        $this->runConsole(
            'doctrine:database:create',
            ['--if-not-exists' => true, '--connection' => 'default']
        );

        // Reset fixtures schemas
        foreach (array_keys($doctrine->getManagers()) as $name) {
            $this->runConsole('doctrine:schema:drop', ['--force' => true, '--em' => $name]);
            $this->runConsole('doctrine:schema:create', ['--em' => $name]);
        }
    }

    protected function tearDown(): void
    {
        $this->kernel->shutdown();
    }

    public function testFixturesLoading(): void
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

    public function testAppendFixtures(): void
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
    public function testFixturesRegisteringUsingInvalidManager(array $inputs, string $expected): void
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

        self::assertFixturesDisplayEquals($expected, $commandTester->getDisplay());
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

        self::assertFixturesDisplayEquals($expected, $commandTester->getDisplay());
    }

    public static function loadCommandProvider(): iterable
    {
        $data = [];

        $data[] = [
            [],
            <<<'EOF'
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Dev/dev.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Prod/prod.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/Resources/fixtures/aentity.php
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Prod/prod.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Dev/dev.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Dev/dev.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/Resources/fixtures/bentity.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Ignored2/notIgnored.yaml
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
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/brand.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/product.yaml
      - /home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Resources/fixtures/Provider/testFormatter.yaml
  > purging database
  > fixtures loaded
EOF
        ];

        // Fix paths
        foreach ($data as $index => $dataSet) {
            $data[$index][1] = str_replace(
                '/home/travis/build/theofidry/AliceBundle',
                getcwd(),
                $dataSet[1],
            );
        }

        return $data;
    }

    protected static function assertFixturesDisplayEquals(string $expected, string $display): void
    {
        $expected = self::normalizeFixturesDisplay($expected);
        $display = self::normalizeFixturesDisplay($display);

        self::assertCount(0, array_diff($expected, $display));
    }

    /**
     * @return string[]
     */
    private static function normalizeFixturesDisplay(string $display): array
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

    private function verifyProducts(int $count): void
    {
        self::assertCount(
            $count,
            $this->defaultEntityManager->getRepository(Product::class)->findAll()
        );

        for ($i = 1; $i <= $count; ++$i) {
            /* @var Product|null $product */
            $product = $this->defaultEntityManager->find(Product::class, $i);
            self::assertNotNull($product);
            self::assertStringStartsWith('Awesome Product', $product->getDescription());
            // Make sure every product has a brand
            self::assertInstanceOf(
                Brand::class,
                $product->getBrand()
            );
        }
    }

    private function verifyBrands(int $count): void
    {
        self::assertCount(
            $count,
            $this->defaultEntityManager->getRepository(Brand::class)->findAll()
        );

        for ($i = 1; $i <= $count; ++$i) {
            /* @var Brand|null $brand */
            $brand = $this->defaultEntityManager->find(Brand::class, $i);
            self::assertNotNull($brand);
        }
    }
}
