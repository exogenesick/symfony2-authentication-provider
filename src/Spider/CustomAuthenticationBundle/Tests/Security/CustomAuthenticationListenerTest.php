<?php

namespace Spider\CustomAuthenticationBundle\Tests\Security;

use Spider\CustomAuthenticationBundle\Security\CustomAuthenticationListener;
use Spider\CustomAuthenticationBundle\Security\CustomToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @coversDefaultClass Spider\CustomAuthenticationBundle\Security\CustomAuthenticationListener
 */
class CustomAuthenticationListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomAuthenticationListener */
    protected $customAuthenticationListener;

    /** @var AuthenticationManagerInterface */
    protected $authenticationManagerMock;

    /** @var SecurityContextInterface */
    protected $securityContextMock;

    /** @var GetResponseEvent */
    protected $getResponseEventMock;

    protected function setUp()
    {
        $this->authenticationManagerMock = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface')
            ->setMethods(array('authenticate'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->securityContextMock = $this
            ->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->setMethods(array('setToken', 'getToken', 'isGranted'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->getResponseEventMock = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->setMethods(array('getRequest', 'setResponse'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->customAuthenticationListener = new CustomAuthenticationListener(
            $this->securityContextMock,
            $this->authenticationManagerMock
        );
    }

    /**
     * @covers ::handle
     * @covers ::throwError
     */
    public function testHandle_ShouldDeniedAccessToResource_WhenRequestHasNotAuthenticationData()
    {
        $this->getResponseEventMock
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue(new Request()));

        $responseStub = new Response();
        $responseStub->setStatusCode(403);

        $this->getResponseEventMock
            ->expects($this->once())
            ->method('setResponse')
            ->with($responseStub);

        $this->customAuthenticationListener->handle($this->getResponseEventMock);
    }

    /**
     * @covers ::handle
     * @covers ::throwError
     */
    public function testHandle_ShouldDeniedAccessToResource_WhenRequestHasInvalidAuthenticationData()
    {
        $requestStub = new Request();
        $requestStub->headers->set('php-auth-user', 'invalid_username');
        $requestStub->headers->set('php-auth-pw', 'invalid_password');

        $this->getResponseEventMock
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($requestStub));

        $responseStub = new Response();
        $responseStub->setStatusCode(403);

        $this->getResponseEventMock
            ->expects($this->once())
            ->method('setResponse')
            ->with($responseStub);

        $this->authenticationManagerMock
            ->expects($this->once())
            ->method('authenticate')
            ->will($this->throwException(new AuthenticationException('Authentication error.')));

        $this->customAuthenticationListener->handle($this->getResponseEventMock);
    }

    /**
     * @covers ::handle
     * @covers ::throwError
     */
    public function testHandle_ShouldCorrectlySetToken_WhenRequestHasValidAuthenticationData()
    {
        $validUsername = 'valid_username';
        $validPassword = 'valid_password';

        $requestStub = new Request();
        $requestStub->headers->set('php-auth-user', $validUsername);
        $requestStub->headers->set('php-auth-pw', $validPassword);

        $this->getResponseEventMock
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($requestStub));

        $token = new CustomToken($validUsername, $validPassword);

        $this->authenticationManagerMock
            ->expects($this->any())
            ->method('authenticate')
            ->will($this->returnValue($token));

        $this->securityContextMock
            ->expects($this->once())
            ->method('setToken')
            ->with($token);

        $this->customAuthenticationListener->handle($this->getResponseEventMock);
    }
}
