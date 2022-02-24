<?php

namespace Divante\Bundle\AdventureBundle;

use Divante\Bundle\AdventureBundle\DependencyInjection\Compiler\MessageBusHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdventureBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MessageBusHandlerPass());
    }
}
