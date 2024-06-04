<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'src/')
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'tests/')
    ->notPath(__DIR__ . DIRECTORY_SEPARATOR . 'src/Kernel.php')
;

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'yoda_style' => false,
        'concat_space' => ['spacing' => 'one'],
        'no_unused_imports' => true,
        //'global_namespace_import' => ['import_classes' => true],
    ])
    ->setFinder($finder)
;
