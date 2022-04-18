<?php

namespace Solar\Control;

use Solar\Router\Request;

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
    public function __invoke(): string;
}