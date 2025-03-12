<?php

namespace MaK\CssModuleBundle\Twig\NodeVisitor;

use MaK\CssModuleBundle\Twig\Node\ImportModuleNode;
use RuntimeException;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class CssScopeNodeVisitor implements NodeVisitorInterface
{
    private Scope $scope;

    public function __construct(private readonly string $projectDir)
    {
        $this->scope = new Scope();
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof BlockNode || $node instanceof ModuleNode) {
            $this->scope = $this->scope->enter();
        }

        if ($node instanceof ImportModuleNode) {
            $this->scope->set('modulePath', $node->getNode('modulePath'));
            if (!$node->getNode('modulePath') instanceof ConstantExpression) {
                throw new RuntimeException('The module path must be a constant expression');
            }

            $node->getNode('modulePath')->setAttribute('value', $this->getPathFromNode($node->getNode('modulePath')));
            return $node;
        }

        if ($node instanceof FunctionExpression
        && 'scope' === $node->getAttribute('name')) {
            $arguments = $node->getNode('arguments');
            if ($this->isNamedArguments($arguments)) {
                if (!$arguments->hasNode('module') && !$arguments->hasNode('0')) {
                    $arguments->setNode('module', $this->scope->get('modulePath'));
                } elseif (!$arguments->hasNode('1')) {
                    $arguments->setNode('1', $this->scope->get('modulePath'));
                }
            } elseif (!$arguments->hasNode('1')) {
                $arguments->setNode('1', $this->scope->get('modulePath'));
            } elseif($arguments->hasNode('1')) {
                $relativePath = $arguments->getNode('1')->getAttribute('value');

                $templateFile = $node->getSourceContext()->getPath();
                $templateDir = dirname($templateFile);

                $modulePath = realpath($templateDir.DIRECTORY_SEPARATOR.$relativePath);

                if(false == $modulePath) {
                    throw new RuntimeException(sprintf('The css module "%s" does not exist.', $node->getAttribute('value')));
                }
                
                $modulePath = str_replace($this->projectDir.'/', '', $modulePath);
                $arguments->getNode('1')->setAttribute('value', $modulePath);
            }
        }

        return $node;
    }

    private function getPathFromNode(ConstantExpression $node): string
    {
        $templateDirectory = dirname($node->getSourceContext()->getPath());

        $moduleFile = realpath($templateDirectory.DIRECTORY_SEPARATOR.$node->getAttribute('value'));

        if (false === $moduleFile && '' !== $templateDirectory) {
            // when running twig:lint, $templateDirectory is a empty string. We ignore that for now
            throw new RuntimeException(sprintf('The css module "%s" does not exist.', $node->getAttribute('value')));
        }

        return ltrim(str_replace($this->projectDir, '', (string) $moduleFile), '/');
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        if ($node instanceof ImportModuleNode) {
            return null;
        }

        if ($node instanceof BlockNode || $node instanceof ModuleNode) {
            $this->scope = $this->scope->leave();
        }

        return $node;
    }

    private function isNamedArguments(Node $arguments): bool
    {
        foreach ($arguments as $name => $node) {
            /* @phpstan-ignore-next-line */
            if (!\is_int($name)) {
                return true;
            }
        }

        return false;
    }

    public function getPriority(): int
    {
        return -10;
    }
}
