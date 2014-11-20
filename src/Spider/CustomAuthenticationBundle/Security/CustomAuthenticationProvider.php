<?php

namespace Spider\CustomAuthenticationBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CustomAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var  UserProviderInterface */
    protected $userProvider;

    /**
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param TokenInterface $token
     *
     * @return CustomToken
     *
     * @throws AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        /** @var CustomUser $user */
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if (null === $user || $user->getPassword() !== $token->getPassword()) {
            throw new AuthenticationException('The authentication failed.');
        }

        $token->setUser($user);
        $token->setAuthenticated(true);

        return $token;
    }

    /**
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof CustomToken;
    }
}