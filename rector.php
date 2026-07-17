<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_85)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        rectorPreset: true
    )
    ->withPaths([
        __DIR__ . '/qltabs.php',
        __DIR__ . '/php',
        __DIR__ . '/tmpl',
    ])// ->withPreparedSets(deadCode: true)
    ;
