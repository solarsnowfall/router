<?php

namespace Solar\Router;

class Curl
{
    /**
     * @var resource $resource
     */
    protected $resource;

    protected string $url;

    public function __construct(?string $url  = null)
    {
        $this->setUrl($url);
    }

    public function setUrl($url)
    {
        $this->url = $url;

        curl_setopt($this->resource, CURLOPT_URL, $this->url);

        return $this;
    }
}