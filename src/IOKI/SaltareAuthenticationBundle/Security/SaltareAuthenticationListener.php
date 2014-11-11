<?php

namespace IOKI\SaltareAuthenticationBundle\Security;

use IOKI\SaltareAuthenticationBundle\Security\SaltareToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class SaltareAuthenticationListener implements ListenerInterface
{
    /** @var SecurityContextInterface */
    protected $securityContext;

    /** @var AuthenticationManagerInterface */
    protected $authenticationManager;

    /**
     * @param SecurityContextInterface $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $request->headers->has('');

        try {
            $saltareToken = new SaltareToken();
            $saltareToken->setUser('Spider');

            $authToken = $this->authenticationManager->authenticate($saltareToken);
            $this->securityContext->setToken($authToken);

            return;
        } catch (AuthenticationException $failed) {
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }

        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}