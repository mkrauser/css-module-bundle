<?php

namespace MaK\CssModuleBundle\TokenParser;

use MaK\CssModuleBundle\Node\ImportModuleNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class ImportModuleTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $expr = $this->parser->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new ImportModuleNode($expr, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'importModule';
    }
}
