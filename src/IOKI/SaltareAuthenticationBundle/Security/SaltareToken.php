<?php

namespace IOKI\SaltareAuthenticationBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class SaltareToken extends AbstractToken
{
    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /**
     * @param string $username
     * @param string $password
     * @param array $roles
     */
    public function __construct($username, $password, array $roles = array())
    {
        parent::__construct($roles);

        $this->username = $username;
        $this->password = $password;
        $this->setAuthenticated(false);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed|string
     */
    public function getCredentials()
    {
        return '';
    }
}