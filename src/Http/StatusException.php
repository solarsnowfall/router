<?php

namespace Solar\Http;

class StatusException extends \Exception
{
    /**
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($code = 0, \Throwable $previous = null)
    {
        $message = StatusCode::defaultMessage($code);

        parent::__construct($message, $code, $previous);
    }
}