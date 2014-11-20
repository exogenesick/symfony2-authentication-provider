<?php

namespace Spider\CustomAuthenticationBundle\Tests\Security;

use Spider\CustomAuthenticationBundle\Security\CustomUserProvider;

/**
 * @coversDefaultClass Spider\CustomAuthenticationBundle\Security\CustomUserProvider
 */
class CustomUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $users = array();

    /** @var CustomUserProvider */
    protected $customUserProvider;

    protected function setUp()
    {
        $this->users[] = array('username' => 'user_username', 'password' => 'user_password');

        $this->customUserProvider = new CustomUserProvider($this->users);
    }

    /**
     * @covers ::loadUserByUsername
     *
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @expectedExceptionMessage User not found
     */
    public function testLoadUserByUsername_ShouldThrowException_WhenUserNotFound()
    {
        $this->customUserProvider->loadUserByUsername('nonexistent_username');
    }

    /**
     * @covers ::loadUserByUsername
     * @covers ::__construct
     */
    public function testLoadUserByUsername_ShouldReturnCorrectUserObject_WhenUserFound()
    {
        $expectedUsername = $this->users[0]['username'];

        $customUser = $this->customUserProvider->loadUserByUsername($expectedUsername);

        $this->assertInstanceOf('Spider\CustomAuthenticationBundle\Security\CustomUser', $customUser);
        $this->assertEquals($expectedUsername, $customUser->getUsername());
    }

    /**
     * @covers ::refreshUser
     *
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshUser_ShouldThrowException_WhenTryToRefreshUserWithUnsupportedObject()
    {
        $userMock = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->customUserProvider->refreshUser($userMock);
    }

    /**
     * @covers ::refreshUser
     */
    public function testRefreshUser_ShouldReturnCorrectUserObject_WhenUserFound()
    {
        $expectedUsername = $this->users[0]['username'];

        $customUserMock = $this->getMockBuilder('Spider\CustomAuthenticationBundle\Security\CustomUser')
            ->setMethods(array('getUsername'))
            ->disableOriginalConstructor()
            ->getMock();

        $customUserMock
            ->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($expectedUsername));

        $customUser = $this->customUserProvider->refreshUser($customUserMock);

        $this->assertInstanceOf('Spider\CustomAuthenticationBundle\Security\CustomUser', $customUser);
        $this->assertEquals($expectedUsername, $customUser->getUsername());
    }

    /**
     * @covers ::supportsClass
     */
    public function testSupportsClass_ShouldReturnTrue_WhenClassNamespaceIsEqual()
    {
        $this->assertTrue(
            $this->customUserProvider->supportsClass('Spider\CustomAuthenticationBundle\Security\CustomUser')
        );
    }
}
