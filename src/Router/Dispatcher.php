<?php

namespace Solar\Router;

use Solar\Auth\AuthInterface;

class Dispatcher
{
    /**
     * @var AuthInterface|null
     */
    protected ?AuthInterface $auth = null;

    /**
     * @var array
     */
    protected array $headers = [];

    /**
     * @var Router
     */
    protected Router $router;

    /**
     * @var int
     */
    protected int $statusCode;

    /**
     * @param Router $router
     * @param AuthInterface|null $auth
     */
    public function __construct(Router $router, AuthInterface $auth = null)
    {
        $this->router = $router;

        $this->auth = $auth;
    }

    /**
     * @param $authCallback
     * @return $this
     */
    public function setAuthCallback($authCallback): self
    {
        $this->authCallback = $authCallback;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->headers['Content-Type'] ?? null;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function dispatch(Request $request)
    {
        try {

            if ($this->auth !== null && !$this->auth->authenticate($request))
                throw new \Exception('Not Authorized', 401);

            if (!$this->router->requestMethodSupported($request->getRequestMethod()))
                throw new \Exception('Method Not Allowed', 405);

            $matches = $this->router->match($request->getRequestMethod(), $request->getRoute());

            $handler = $matches['exact'] ?: $matches['wildcard'];

            if (!$handler)
                throw new \Exception('Not Found', 404);

            $container = new HandlerContainer($handler);

            try {

                set_error_handler(function($code, $message){
                    throw new \Exception($message, $code);
                }, E_ALL);

                $response = $container($request);

                restore_error_handler();

                $this->statusCode = 200;

            } catch (\Exception $exception) {

                error_log($exception->getMessage());

                throw new \Exception('Internal Server Error', 500, $exception);
            }

        } catch (\Exception $exception) {

            $response = $this->errorResponse($exception->getMessage(), $exception->getCode());

            $this->statusCode = $exception->getCode();

        } finally {

            $this->sendHeaders();

            echo $response ?? '';
        }
    }

    /**
     * @param string $message
     * @param int $code
     * @return string
     */
    protected function errorResponse(string $message, int $code): string
    {
        if ($this->getContentType() === 'application/json')
            return json_encode(['message' => $message, 'code' => $code]);

        return "<html lang='en'><head><title>$code $message</title></head><body><h1>$message</h1></body></html>";
    }

    /**
     * @return $this
     */
    protected function sendHeaders(): Dispatcher
    {
        foreach ($this->headers as $key => $value)
            header("$key: $value");

        return $this;
    }
}