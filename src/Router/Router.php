<?php

namespace Solar\Router;

class Router
{
    /**
     * @var array[]
     */
    protected array $handlers = [];

    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @var string[]
     */
    protected array $supportedHttpMethods = [
        'DELETE',
        'GET',
        'POST',
        'PUT'
    ];

    protected Request $request;

    /**
     * @param string $method
     * @param string $route
     * @return array
     */
    public function match(string $method, string $route): array
    {
        $matches = ['exact' => false, 'wildcard' => false];

        if (isset($this->handlers[$method]))
        {
            if (array_key_exists($route, $this->handlers[$method]))
                $matches['exact'] = $this->handlers[$method][$route];

            $wildcard = substr($route, 0, strrpos($route, '/') + 1) . '*';

            if (array_key_exists($wildcard, $this->handlers[$method]))
                $matches['wildcard'] = $this->handlers[$method][$wildcard];
        }

        return $matches;
    }

    /**
     * @param string $route
     * @param $handler
     * @return $this
     */
    public function delete(string $route, $handler): Router
    {
        return $this->registerHandler('DELETE', $route, $handler);
    }

    /**
     * @param string $route
     * @param $handler
     * @return $this
     */
    public function get(string $route, $handler): Router
    {
        return $this->registerHandler('GET', $route, $handler);
    }

    /**
     * @param string $route
     * @param $handler
     * @return $this
     */
    public function post(string $route, $handler): Router
    {
        return $this->registerHandler('POST', $route, $handler);
    }

    /**
     * @param string $route
     * @param $handler
     * @return $this
     */
    public function put(string $route, $handler): Router
    {
        return $this->registerHandler('PUT', $route, $handler);
    }

    /**
     * @param string $requestMethod
     * @return bool
     */
    public function requestMethodSupported(string $requestMethod): bool
    {
        return in_array(strtoupper($requestMethod), $this->supportedHttpMethods);
    }

    /**
     * @param string $requestMethod
     * @param string $route
     * @param $handler
     * @return $this
     */
    protected function registerHandler(string $requestMethod, string $route, $handler): Router
    {
        $this->handlers[$requestMethod][$route] = $handler;

        return $this;
    }
}