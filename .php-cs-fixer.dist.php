<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())->in([
    __DIR__ . '/src',
    __DIR__ . '/tests',
])->notPath([
    'MockHelper.php'
]);

// TODO: Update the configuration after raising the minimum PHP version
return (new Config())
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@PER-CS2.0' => true,
        'nullable_type_declaration' => true,
        'operator_linebreak' => true,
        'ordered_types' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'single_class_element_per_statement' => true,
        'types_spaces' => true,
        'no_unused_imports' => true,
        'ordered_class_elements' => true,
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'declare_strict_types' => true,
        'native_function_invocation' => true,
        'native_constant_invocation' => true,
        'fully_qualified_strict_types' => [
            'import_symbols' => true
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
    ])
    ->setFinder($finder);
