<?php

namespace MaK\CssModuleBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class MakCssModuleExtension extends Extension
{

    /** @inheritDoc */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('mak_css_module.localIdentContext', $config['localIdentContext']);
        $container->setParameter('mak_css_module.localIdentName', $config['localIdentName']);
        $container->setParameter('mak_css_module.localIdentHashSalt', $config['localIdentHashSalt']);
    }

    public function getAlias(): string
    {
        return "mak_css_module";
    }
}