<?php

/*
 * This file is part of the CssModuleBundle package.
 * Copyright (c) Matthias Krauser <matthias@krauser.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mak\CssModuleBundle\Tests\Extension;

use MaK\CssModuleBundle\Extension\CssModuleExtension;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class CssModuleExtensionTest extends TestCase
{

    public function testImportModuleTag(): void
    {
        $extension = new CssModuleExtension(__DIR__);
        $twig = new Environment(new ArrayLoader(['template' => 'A{% importModule "button.module.scss" %}B']), [
            'cache' => false,
            'optimizations' => 0,
        ]);
        $twig->addExtension($extension);

        $this->assertSame('AB', $twig->render('template'));
    }

    /** @param string|string[] $css */
    #[TestWith(['a', 'bMK2w'])]
    #[TestWith([['a', 'b'], 'bMK2w jeZuO'])]
    public function testCssScope(string|array $css, string $expectedHashes): void
    {
        $extension = new CssModuleExtension(__DIR__);

        $this->assertSame($expectedHashes, $extension->cssScope($css, __DIR__));
    }
}