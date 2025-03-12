<?php

namespace MaK\CssModuleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('css_module');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('localIdentName')
                ->defaultValue('[hash:base64]')
            ->end()
            ->scalarNode('localIdentContext')
                ->defaultValue('%kernel.project_dir%')
            ->end()
            ->scalarNode('localIdentHashSalt')
                ->defaultNull()
            ->end()
        ;

        return $treeBuilder;
    }
}