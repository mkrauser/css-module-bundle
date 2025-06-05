<?php

namespace MaK\CssModuleBundle\Tests\Extension;

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

        $this->assertEquals('AB', $twig->render('template'));
    }

    /** @param string|string[] $css */
    #[TestWith(['a', 'bMK2w'])]
    #[TestWith([['a', 'b'], 'bMK2w jeZuO'])]
    public function testCssScope(string|array $css, string $expectedHashes): void
    {
        $extension = new CssModuleExtension(__DIR__);

        $this->assertEquals($expectedHashes, $extension->cssScope($css, __DIR__));
    }
}