<?php

namespace Spider\CustomAuthenticationBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class CustomSecurityFactory implements SecurityFactoryInterface
{
    /**
     * @param ContainerBuilder $container
     * @param string           $id
     * @param array            $config
     * @param string           $userProvider
     * @param mixed            $defaultEntryPoint
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.custom_authentication.' . $id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('custom_authentication.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.custom_authentication.' . $id;
        $container
            ->setDefinition($listenerId, new DefinitionDecorator('custom_authentication.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'custom_authentication';
    }

    /**
     * @param NodeDefinition $builder
     */
    public function addConfiguration(NodeDefinition $builder) {}
}
