<?php

namespace Solar\Control;

use Solar\Router\Request;
use Solar\Router\Response;

interface ControlInterface
{
    /**
     * @param Request $request
     * @param string $method
     */
    public function __construct(Request $request, string $method);

    /**
     * @return string
     */
    public function __invoke(): Response;
}