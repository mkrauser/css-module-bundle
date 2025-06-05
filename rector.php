<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/DependencyInjection',
        __DIR__.'/Exception',
        __DIR__.'/Extension',
        __DIR__.'/Node',
        __DIR__.'/NodeVisitor',
        __DIR__.'/Resources/config',
        __DIR__.'/Tests',
        __DIR__.'/TokenParser',
    ])
    ->withPhpSets(php82: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
    )
    ->withSets([
        SymfonySetList::SYMFONY_64,
        PHPUnitSetList::PHPUNIT_110,
    ])
    ->withAttributesSets(
        symfony: true,
        doctrine: true,
        phpunit: true,
    );