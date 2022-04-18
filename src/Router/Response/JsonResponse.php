<?php

namespace Solar\Router\Response;

class JsonResponse extends AbstractResponse
{
    /**
     * @return string
     */
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