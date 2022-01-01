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

namespace Hautelook\AliceBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * @covers \Hautelook\AliceBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $expected = [
            'fixtures_path' => ['Resources/fixtures'],
            'root_dirs' => [
                '%kernel.project_dir%',
            ],
        ];

        $actual = $processor->processConfiguration($configuration, []);

        self::assertEquals($expected, $actual);
    }

    public function testDefaultValuesCanBeOverridden(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $expected = [
            'fixtures_path' => ['/Resources/path/to/fixtures'],
            'root_dirs' => [
                'my/root/dir',
            ],
        ];

        $actual = $processor->processConfiguration(
            $configuration,
            [
                'hautelook_alice' => [
                    'fixtures_path' => ['/Resources/path/to/fixtures'],
                    'root_dirs' => [
                        'my/root/dir',
                    ],
                ],
            ]
        );

        self::assertEquals($expected, $actual);
    }

    public function testSingleFixturePathIsConvertedToArray(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $expected = [
            'fixtures_path' => ['/Resources/path/to/fixtures'],
            'root_dirs' => [
                'my/root/dir',
            ],
        ];

        $actual = $processor->processConfiguration(
            $configuration,
            [
                'hautelook_alice' => [
                    'fixtures_path' => '/Resources/path/to/fixtures',
                    'root_dirs' => [
                        'my/root/dir',
                    ],
                ],
            ]
        );

        self::assertEquals($expected, $actual);
    }
}
