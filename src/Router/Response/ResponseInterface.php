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

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return int
     */
    public function getStatus(): int;


}