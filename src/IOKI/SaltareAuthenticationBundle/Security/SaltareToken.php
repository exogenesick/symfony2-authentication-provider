<?php

namespace IOKI\SaltareAuthenticationBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class SaltareToken extends AbstractToken
{
    /**
     * @param array $roles
     */
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);
        $this->setAuthenticated(true);
    }

    /**
     * @return mixed|string
     */
    public function getCredentials()
    {
        return '';
    }
}