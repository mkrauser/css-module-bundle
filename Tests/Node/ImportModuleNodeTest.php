<?php

namespace MaK\CssModuleBundle\Tests\Node;

use MaK\CssModuleBundle\Node\ImportModuleNode;
use PHPUnit\Framework\TestCase;
use Twig\Compiler;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\TextNode;

class ImportModuleNodeTest extends TestCase
{
    public function testCompileStrict(): void
    {
        $file = new ConstantExpression('test.module.css', 0);
        $node = new ImportModuleNode($file, 0);


        $env = new Environment($this->createMock(LoaderInterface::class), ['strict_variables' => true]);
        $compiler = new Compiler($env);

        $this->assertEquals('', trim($compiler->compile($node)->getSource()));
    }
}