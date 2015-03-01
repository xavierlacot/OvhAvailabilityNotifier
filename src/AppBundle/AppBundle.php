<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use AppBundle\DependencyInjection\Compiler\NotifierCompilerPass;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new NotifierCompilerPass());
    }
}
