<?php

namespace IOKI\SaltareAuthenticationBundle\Security;

use IOKI\SaltareAuthenticationBundle\Security\SaltareToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SaltareAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var  UserProviderInterface */
    protected $userProvider;

    /**
     * @param UserProviderInterface $userProvider
     */
    function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param TokenInterface $token
     *
     * @return SaltareToken
     */
    public function authenticate(TokenInterface $token)
    {
        $authenticatedToken = new SaltareToken();
        $authenticatedToken->setUser('Spider');

        return $authenticatedToken;

        //throw new AuthenticationException('The authentication failed.');
    }

    /**
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof SaltareToken;
    }
}