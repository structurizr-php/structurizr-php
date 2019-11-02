<?php

$fileHeaderComment = <<<COMMENT
This file is part of the Structurizr for PHP.

(c) Norbert Orzechowicz <norbert@orzechowicz.pl>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/examples',
    ]);

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__.'/.php_cs.cache')
    ->setRules([
        '@PSR2' => true,
        'psr4' => true,
        'strict_param' => true,
        'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both'],
        'ordered_imports' => true,
        'blank_line_before_statement' => true,
        'trailing_comma_in_multiline_array' => true,
        'strict_comparison' => true,
    ])
    ->setFinder($finder);