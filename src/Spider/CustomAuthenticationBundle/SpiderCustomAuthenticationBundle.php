<?php

namespace Spider\CustomAuthenticationBundle;

use Spider\CustomAuthenticationBundle\Security\CustomSecurityFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SpiderCustomAuthenticationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new CustomSecurityFactory());
    }
}
