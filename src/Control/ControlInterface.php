<?php

namespace Solar\Control;

use Solar\Router\Request;
use Solar\Router\Response\ResponseInterface;

interface ControlInterface
{
    /**
     * @param Request $request
     * @param string $method
     */
    public function __construct(Request $request, string $method);

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface;
}