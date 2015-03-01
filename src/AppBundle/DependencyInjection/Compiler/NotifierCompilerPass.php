<?php
namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class NotifierCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('app.ovh.notifier')) {
            return;
        }

        $definition = $container->getDefinition('app.ovh.notifier');

        foreach ($container->findTaggedServiceIds('ovh.notifier') as $id => $attributes) {
            $definition->addMethodCall('addNotifier', array(new Reference($id)));
        }
    }
}
