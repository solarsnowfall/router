<?php

namespace Solar\Router;

class HandlerContainer
{
    /**
     * @var mixed
     */
    protected $handler;

    /**
     * @param mixed $handler
     */
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param Request $request
     * @return false|mixed
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        $handler = $this->handler;

        if ($handler instanceof \Closure)
            return $handler($request);

        if (is_string($handler) && strpos($this->handler, '::') !== false)
            $handler = explode('::', $handler);

        if (is_array($handler) && method_exists($handler[0], $handler[1]))
        {
            $reflection = new \ReflectionMethod($handler[0], $handler[1]);

            if ($reflection->isStatic())
                return forward_static_call($handler, $request);

            $handler[0] = new $handler[0]($request);

            return call_user_func($handler);
        }

        throw new \Exception('Invalid handler provided');
    }
}