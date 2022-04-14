<?php

namespace Solar\Control;

use Solar\Router\Request;

interface ControlInterface
{
    /**
     * @param Request $request
     */
    public function __construct(Request $request);
}