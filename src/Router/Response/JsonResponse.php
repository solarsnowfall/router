<?php

namespace Solar\Router;

use Solar\Router\Response\AbstractResponse;

class JsonResponse extends AbstractResponse
{
    public function __toString(): string
    {
        $data = [
            'status'    => $this->status,
            'message'   => $this->message,
            'data'      => $this->data
        ];

        return json_encode($data, ENT_QUOTES);
    }
}