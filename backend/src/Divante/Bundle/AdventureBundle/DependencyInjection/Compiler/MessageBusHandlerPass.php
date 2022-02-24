<?php

namespace Divante\Bundle\AdventureBundle\DependencyInjection\Compiler;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\MessageBusCreator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MessageBusHandlerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container) : void
    {
        if (!$container->has(MessageBusCreator::class)) {
            return;
        }

        $definition = $container->findDefinition(MessageBusCreator::class);
        $taggedServices = $container->findTaggedServiceIds('messenger.message_handler');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
