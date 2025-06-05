<?php

/*
 * This file is part of the CssModuleBundle package.
 * Copyright (c) Matthias Krauser <matthias@krauser.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MaK\CssModuleBundle\Tests\NodeVisitor;

use PHPUnit\Framework\TestCase;
use MaK\CssModuleBundle\NodeVisitor\Scope;

class ScopeTest extends TestCase
{
    public function testScopeInitiation(): void
    {
        $scope = new Scope();
        $scope->enter();
        $this->assertNull($scope->get('test'));
    }
}