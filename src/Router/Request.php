<?php

namespace Solar\Router;

class Request
{
    protected ?string $contentType = null;

    protected string $remoteAddress;

    protected string $requestMethod;

    protected string $requestUri;

    protected string $route;

    protected string $scriptName;

    protected string $serverProtocol;

    public function __construct()
    {
        $this->contentType = $_SERVER['CONTENT_TYPE'] ?? null;

        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];

        $this->requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

        $this->requestUri = $_SERVER['REQUEST_URI'];

        $this->scriptName = $_SERVER['SCRIPT_NAME'];

        $this->serverProtocol = $_SERVER['SERVER_PROTOCOL'];

        $this->route = $this->parseRoute();

        //var_dump(get_object_vars($this));
    }

    public function getBody()
    {
        $data = $body = [];

        switch ($this->requestMethod)
        {
            case 'DELETE':
            case 'PUT':

                $data = file_get_contents('php://input');

                if ($this->contentType === 'application/json')
                    $data = json_decode($data, true);

                break;

            case 'GET':

                $data = $_GET;
                break;

            case 'POST':

               $data = $this->contentType === 'application/json'
                   ? json_decode(file_get_contents('php://input'), true)
                   : $_POST;

                break;
        }

        foreach ($data as $key => $value)
            $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);

        return $body;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    public function getRequestUri()
    {
        return $this->requestUri;
    }

    public function getScriptName()
    {
        return $this->scriptName;
    }

    public function getServerProtocol()
    {
        return $this->serverProtocol;
    }

    protected function parseRoute(): string
    {
        $parsed = parse_url($this->requestUri);

        $path = explode('/', $parsed['path']);

        $parsed = parse_url($this->scriptName);

        $script = explode('/', $parsed['path']);

        $route = array_diff($path, $script);

        return '/' . implode('/', $route);
    }
}