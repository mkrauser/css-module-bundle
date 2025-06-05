<?php

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