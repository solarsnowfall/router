<?php

namespace Solar\Router;

class Request
{
    /**
     * @var string
     */
    protected string $authPassword;

    /**
     * @var string
     */
    protected string $authType;

    /**
     * @var string
     */
    protected string $authUser;

    /**
     * @var string|null
     */
    protected ?string $contentType = null;

    /**
     * @var string
     */
    protected string $remoteAddress;

    /**
     * @var string
     */
    protected string $requestMethod;

    /**
     * @var string|mixed
     */
    protected string $requestUri;

    /**
     * @var string
     */
    protected string $route;

    /**
     * @var string
     */
    protected string $scriptName;

    /**
     * @var string
     */
    protected string $serverProtocol;

    /**
     *
     */
    public function __construct()
    {
        $this->contentType = $_SERVER['CONTENT_TYPE'] ?? null;

        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];

        $this->requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

        $this->requestUri = $_SERVER['REQUEST_URI'];

        $this->scriptName = $_SERVER['SCRIPT_NAME'];

        $this->serverProtocol = $_SERVER['SERVER_PROTOCOL'];

        $this->route = $this->parseRoute();

        $this->authType = $this->resolveAuthType();

        $this->authPassword = $_SERVER['PHP_AUTH_PW'] ?? '';

        $this->authUser = $_SERVER['PHP_AUTH_USER'] ?? '';
    }

    /**
     * @return array
     */
    public function getBody(): array
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

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->authPassword;
    }

    /**
     * @return string
     */
    public function getAuthType(): string
    {
        return $this->authType;
    }

    /**
     * @return string
     */
    public function getAuthUser(): string
    {
        return $this->authUser;
    }

    /**
     * @return mixed|string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @return mixed|string
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }

    /**
     * @return mixed|string
     */
    public function getServerProtocol()
    {
        return $this->serverProtocol;
    }

    /**
     * @return string
     */
    protected function parseRoute(): string
    {
        $parsed = parse_url($this->requestUri);

        $path = explode('/', $parsed['path']);

        $parsed = parse_url($this->scriptName);

        $script = explode('/', $parsed['path']);

        $route = array_diff($path, $script);

        return '/' . implode('/', $route);
    }

    /**
     * @return string
     */
    protected function resolveAuthType(): string
    {
        if (!empty($_SERVER['AUTH_TYPE']))
            return $_SERVER['AUTH_TYPE'];

        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']))
            return 'Basic';

        return '';
    }
}