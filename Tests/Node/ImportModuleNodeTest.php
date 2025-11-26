<?php

/*
 * This file is part of the CssModuleBundle package.
 * Copyright (c) Matthias Krauser <matthias@krauser.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mak\CssModuleBundle\Tests\Node;

use Mak\CssModuleBundle\Node\ImportModuleNode;
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

        $this->assertSame('', trim($compiler->compile($node)->getSource()));
    }
}