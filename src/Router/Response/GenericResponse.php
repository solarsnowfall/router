<?php

namespace Solar\Router\Response;

use Solar\Http\StatusCode;

class GenericResponse extends AbstractResponse
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->status >= StatusCode::OK && $this->status <= StatusCode::IM_USED)
            return $this->message;

        return "<html lang='en'><head><title>$this->status $this->message</title></head><body><h1>$this->message</h1></body></html>";
    }
}