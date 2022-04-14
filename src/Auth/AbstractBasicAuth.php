<?php

namespace Solar\Auth;

use Solar\Router\Request;

abstract class AbstractBasicAuth implements AuthInterface
{
    /**
     * @param Request $request
     * @return AuthCredentials
     */
    public function getAuthCredentials(Request $request): AuthCredentials
    {
        return new AuthCredentials($request->getAuthUser(), $request->getAuthPassword());
    }
}