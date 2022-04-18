<?php

namespace Solar\Router\Response;

abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var int
     */
    protected int $status;

    /**
     * @var string
     */
    protected string $message;

    /**
     * @param int $status
     * @param string $message
     * @param array $data
     */
    public function __construct(int $status, string $message, array $data = [])
    {
        $this->status = $status;

        $this->message = $message;

        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}