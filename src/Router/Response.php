<?php

namespace Solar\Router;

class Response
{
    /**
     * @var int
     */
    protected int $code;

    /**
     * @var string|array
     */
    protected $body;

    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     * @param int $code
     * @param $body
     */
    public function __construct(Dispatcher $dispatcher, int $code, $body)
    {
        $this->dispatcher = $dispatcher;

        $this->code = $code;

        $this->body = $body;
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        if ($this->dispatcher->getContentType() === 'application/json' && is_array($this->body))
            return json_encode($this->body);

        return (string) $this->body;
    }
}