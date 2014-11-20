<?php

namespace Spider\CustomAuthenticationBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class CustomUser implements UserInterface
{
    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /**
     * @param string $username
     * @param string $password
     * @param array  $roles
     */
    public function __construct($username, $password, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the roles granted to the user.
     */
    public function getRoles() {}

    /**
     * Returns the salt that was originally used to encode the password.
     */
    public function getSalt() {}

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials() {}
}