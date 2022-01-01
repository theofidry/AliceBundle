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

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

const HEADER = <<<'HEADER'
This file is part of the Hautelook\AliceBundle package.

(c) Baldur Rensch <brensch@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->in(__DIR__.'/fixtures')
    ->exclude('fixtures/Resolver/cache')
    ->exclude('fixtures/Functional/cache')
    ->append([
        __DIR__.'/.php-cs-fixer.dist.php',
        __DIR__.'/bin/console',
    ])
;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'blank_line_after_opening_tag' => true,
        'cast_spaces' => true,
        'combine_consecutive_unsets' => true,
        'declare_equal_normalize' => true,
        'declare_strict_types' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'inheritDoc',
            ],
        ],
        'header_comment' => [
            'header' => HEADER,
            'location' => 'after_open',
        ],
        'include' => true,
        'lowercase_cast' => true,
        'modernize_types_casting' => true,
        'native_function_casing' => true,
        'new_with_braces' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_short_bool_cast' => true,
        'no_spaces_around_offset' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_trim' => true,
        'php_unit_fqcn_annotation' => true,
        'php_unit_test_class_requires_covers' => true,
        'single_quote' => true,
        'space_after_semicolon' => true,
        'standardize_not_equals' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder)
;
