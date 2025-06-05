<?php

/*
 * This file is part of the CssModuleBundle package.
 * Copyright (c) Matthias Krauser <matthias@krauser.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
