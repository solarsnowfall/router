<?php

namespace Solar\Router\Response;

interface ResponseInterface
{
    /**
     * @param int $status
     * @param string $message
     * @param array $data
     */
    public function __construct(int $status, string $message, array $data = []);

    /**
     * @return string
     */
    public function __toString(): string;
}