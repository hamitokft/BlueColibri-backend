<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS2.0' => true,
        '@PHP83Migration' => true,
        '@PhpCsFixer' => true,
        '@PSR1' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
    ->setIndent("    ")
    ->setLineEnding("\n")
;
