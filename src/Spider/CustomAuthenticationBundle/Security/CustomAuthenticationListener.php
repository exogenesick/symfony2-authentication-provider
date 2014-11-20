<?php

namespace Spider\CustomAuthenticationBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class CustomAuthenticationListener implements ListenerInterface
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
        /** @var Request $request */
        $request = $event->getRequest();

        if (!$request->headers->has('php-auth-user') || !$request->headers->has('php-auth-pw')) {
            $this->throwError($event);

            return;
        }

        $token = new CustomToken($request->headers->get('php-auth-user'), $request->headers->get('php-auth-pw'));

        try {
            $this->securityContext->setToken($this->authenticationManager->authenticate($token));

            return;
        } catch (AuthenticationException $e) {
            $this->throwError($event);
        }
    }

    /**
     * @param GetResponseEvent $event
     */
    private function throwError(GetResponseEvent $event)
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $event->setResponse($response);
    }
}