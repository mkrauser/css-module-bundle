<?php

namespace MaK\CssModuleBundle\Twig\TokenParser;

use MaK\CssModuleBundle\Twig\Node\CssModuleNode;
use MaK\CssModuleBundle\Twig\Node\ImportModuleNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class ImportModuleTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new ImportModuleNode($expr, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'importModule';
    }
}
