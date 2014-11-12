<?php

namespace IOKI\SaltareAuthenticationBundle;

use IOKI\SaltareAuthenticationBundle\Security\SaltareSecurityFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IOKISaltareAuthenticationBundle extends Bundle
{
    /**
     * Extend security context with Saltare security
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SaltareSecurityFactory());
    }
}
