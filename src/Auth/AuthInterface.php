<?php

namespace Solar\Auth;

use Solar\Router\Request;

interface AuthInterface
{
    /**
     * @param Request $request
     * @return bool
     */
    public function authenticate(Request $request): bool;

    /**
     * @param Request $request
     * @return AuthCredentials
     */
    public function getAuthCredentials(Request $request): AuthCredentials;
}