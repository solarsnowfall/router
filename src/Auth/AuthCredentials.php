<?php

namespace Solar\Auth;

class AuthCredentials
{
    /**
     * @var string
     */
    protected string $password;

    /**
     * @var string
     */
    protected string $username;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username = '', string $password = '')
    {
        $this->username = $username;

        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}