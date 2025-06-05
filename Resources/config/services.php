<?php

namespace MaK\CssModuleBundle\Component\DependencyInjection\Loader\Configurator;

use MaK\CssModuleBundle\Extension\CssModuleExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('twig.extension.mak_css_module', CssModuleExtension::class)
        ->arg(0, param('mak_css_module.localIdentContext'))
        ->arg(1, param('mak_css_module.localIdentName'))
        ->arg(2, param('mak_css_module.localIdentHashSalt'))
        ->tag('twig.extension');
};