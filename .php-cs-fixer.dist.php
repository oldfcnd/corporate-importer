<?php

/**
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer
 */

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'tests')
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'src')
    ->append(['.php-cs-fixer.dist.php']);

$rules = [
    '@Symfony' => true,
    'yoda_style' => false,
    'concat_space' => ['spacing' => 'one'],
    'not_operator_with_successor_space' => true,
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
    'phpdoc_line_span' => true,
    'phpdoc_order' => true,
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last', 'sort_algorithm' => 'alpha'
    ],
    'phpdoc_var_annotation_correct_order' => true,
];

return (new PhpCsFixer\Config())
    ->setUsingCache(true)
    ->setRules($rules)
    ->setFinder($finder);