<?php

namespace Spider\CustomAuthenticationBundle\Tests\Security;

use Spider\CustomAuthenticationBundle\Security\CustomAuthenticationProvider;
use Spider\CustomAuthenticationBundle\Security\CustomToken;
use Spider\CustomAuthenticationBundle\Security\CustomUserProvider;

/**
 * @coversDefaultClass Spider\CustomAuthenticationBundle\Security\CustomAuthenticationProvider
 */
class CustomAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomAuthenticationProvider */
    protected $customAuthenticationProvider;

    /** @var CustomUserProvider */
    protected $customUserProviderMock;

    /** @var CustomToken */
    protected $customTokenMock;

    protected function setUp()
    {
        $this->customUserProviderMock = $this
            ->getMockBuilder('Spider\CustomAuthenticationBundle\Security\CustomUserProvider')
            ->setMethods(array('loadUserByUsername'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->customTokenMock = $this
            ->getMockBuilder('Spider\CustomAuthenticationBundle\Security\CustomToken')
            ->setMethods(array('getPassword', 'setAuthenticated'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->customAuthenticationProvider = new CustomAuthenticationProvider($this->customUserProviderMock);
    }

    /**
     * @covers ::authenticate
     *
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     * @expectedExceptionMessage The authentication failed.
     */
    public function testAuthenticate_ShouldThrowException_WhenUserIsNotFound()
    {
        $this->customUserProviderMock
            ->expects($this->any())
            ->method('loadUserByUsername')
            ->will($this->returnValue(null));

        $this->customAuthenticationProvider->authenticate($this->customTokenMock);
    }

    /**
     * @covers ::authenticate
     *
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     * @expectedExceptionMessage The authentication failed.
     */
    public function testAuthenticate_ShouldThrowException_WhenUserFoundAndPasswordIsInvalid()
    {
        $userPassword = 'password';
        $tokenPassword = 'some_different_password';

        $this->customTokenMock
            ->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($tokenPassword));

        $customUserMock = $this->getCustomUserMock();
        $customUserMock
            ->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($userPassword));

        $this->customUserProviderMock
            ->expects($this->any())
            ->method('loadUserByUsername')
            ->will($this->returnValue($customUserMock));

        $this->customAuthenticationProvider->authenticate($this->customTokenMock);
    }

    /**
     * @covers ::authenticate
     */
    public function testAuthenticate_ShouldReturnAuthenticatedToken_WhenPassCorrectUserAuthenticationData()
    {
        $password = 'password';

        $this->customTokenMock
            ->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($password));

        $this->customTokenMock
            ->expects($this->once())
            ->method('setAuthenticated')
            ->with($this->equalTo(true));

        $customUserMock = $this->getCustomUserMock();
        $customUserMock
            ->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($password));

        $this->customUserProviderMock
            ->expects($this->any())
            ->method('loadUserByUsername')
            ->will($this->returnValue($customUserMock));

        $authenticatedToken = $this->customAuthenticationProvider->authenticate($this->customTokenMock);
        $this->assertEquals($this->customTokenMock, $authenticatedToken);
        $this->assertEquals($password, $authenticatedToken->getPassword());
    }

    /**
     * @covers ::supports
     */
    public function testSupports_ShouldReturnTrue_WhenSupportsGivenInstance()
    {
        $this->assertTrue($this->customAuthenticationProvider->supports($this->customTokenMock));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCustomUserMock()
    {
        return $this
            ->getMockBuilder('Spider\CustomAuthenticationBundle\Security\CustomUser')
            ->setMethods(array('getPassword'))
            ->disableOriginalConstructor()
            ->getMock();
    }
}
