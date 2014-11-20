<?php

namespace Spider\CustomAuthenticationBundle\Tests\Security;

use Spider\CustomAuthenticationBundle\Security\CustomSecurityFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @coversDefaultClass Spider\CustomAuthenticationBundle\Security\CustomSecurityFactory
 */
class CustomSecurityFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::create
     */
    public function testCreate_ShouldCreateServices_BasedOnExistentServicesInContainer()
    {
        $id = 'custom_authentication';
        $expectedSecurityContextProviderId = 'security.authentication.provider.custom_authentication.' . $id;
        $expectedSecurityContextListenerId = 'security.authentication.listener.custom_authentication.' . $id;

        $customAuthenticationProviderId = 'custom_authentication.authentication.provider';
        $customAuthenticationListenerId = 'custom_authentication.authentication.listener';
        $customUserProviderId = 'custom_authentication.users';

        $customAuthenticationProviderMock = $this
            ->getMockBuilder('Spider\CustomAuthenticationBundle\Security\CustomAuthenticationProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $customAuthenticationListenerMock = $this
            ->getMockBuilder('Spider\CustomAuthenticationBundle\Security\CustomAuthenticationListener')
            ->disableOriginalConstructor()
            ->getMock();

        $customUserProviderMock = $this
            ->getMockBuilder('Spider\CustomAuthenticationBundle\Security\CustomUserProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $container = new ContainerBuilder();
        $container->set($customAuthenticationProviderId, $customAuthenticationProviderMock);
        $container->set($customAuthenticationListenerId, $customAuthenticationListenerMock);
        $container->set($customUserProviderId, $customUserProviderMock);

        $customSecurityFactory = new CustomSecurityFactory();
        list($securityContextProviderId, $securityContextListenerId) =
            $customSecurityFactory->create($container, $id, null, $customUserProviderId, null);

        $this->assertEquals(
            $expectedSecurityContextProviderId,
            $securityContextProviderId,
            'Service identifier of security context provider is not the same.'
        );
        $this->assertEquals(
            $expectedSecurityContextListenerId,
            $securityContextListenerId,
            'Service identifier of security context listener is not the same.'
        );
        $this->assertEquals(
            $customAuthenticationProviderMock,
            $container->get($customAuthenticationProviderId),
            'Expected custom authentication provider instance is not the same.'
        );
        $this->assertEquals(
            $customAuthenticationListenerMock,
            $container->get($customAuthenticationListenerId),
            'Expected custom authentication listener instance is not the same.'
            );
        $this->assertEquals(
            $customUserProviderMock,
            $container->get($customUserProviderId),
            'Expected custom user provider instance is not the same.'
        );
    }
}
