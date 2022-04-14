<?php

namespace Solar\Router;

use Solar\String\Convention;

class ControllerRouter extends Router
{
    /**
     * @var string
     */
    protected string $classSuffix;

    /**
     * @var bool
     */
    protected bool $dynamic;

    /**
     * @var string
     */
    protected string $namespace;

    /**
     * @param string $namespace
     * @param bool $dynamic
     * @param string $classSuffix
     */
    public function __construct(string $namespace = '', bool $dynamic = false, string $classSuffix = 'Controller')
    {
        $this->namespace = rtrim($namespace, '\\');

        $this->dynamic = $dynamic;

        $this->classSuffix = $classSuffix;
    }

    /**
     *
     * @param string $method
     * @param string $route
     * @return array
     */
    public function match(string $method, string $route): array
    {
        $matches = parent::match($method, $route);

        if ($this->dynamic || ($matches['exact'] === null || $matches['wildcard'] === null))
        {
            $parts = explode('/', ltrim($route, '/'));

            $class = $this->namespace . '\\' . Convention::toCamelCase($parts[0]) . $this->classSuffix;

            $method = Convention::toLowerCamelCase($parts[1]);

            $handler = [$class, $method];

            if ($matches['exact'] === null)
                $matches['exact'] = $handler;
            else
                $matches['wildcard'] = $handler;
        }

        return $matches;
    }

    /**
     * @param string $route
     * @param callable|null $handler
     * @return $this
     */
    public function delete(string $route, $handler = null): self
    {
        return parent::delete($route, $handler);
    }

    /**
     * @param string $route
     * @param callable|null $handler
     * @return $this
     */
    public function get(string $route, $handler = null): self
    {
        return parent::get($route, $handler);
    }

    /**
     * @param string $route
     * @param callable|null $handler
     * @return $this
     */
    public function post(string $route, $handler = null): self
    {
        return parent::post($route, $handler);
    }

    /**
     * @param string $route
     * @param callable|null $handler
     * @return $this
     */
    public function put(string $route, $handler = null): self
    {
        return parent::put($route, $handler);
    }
}