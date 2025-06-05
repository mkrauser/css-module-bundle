<?php

namespace MaK\CssModuleBundle\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

final class ImportModuleNode extends Node
{
    public function __construct(AbstractExpression $expr, int $lineno, ?string $tag = null)
    {
        parent::__construct(['modulePath' => $expr], [], $lineno, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        // noop as this node is just a marker for CssScopeNodeVisitor
    }
}
