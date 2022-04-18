<?php

namespace Solar\Router;

use Solar\Auth\AuthInterface;
use Solar\Http\StatusCode;
use Solar\Http\StatusException;

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
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->headers['Content-Type'] ?? null;
    }

    /**
     * @param string $contentType
     * @return void
     */
    public function setContentType(string $contentType)
    {
        $this->headers['Content-Type'] = $contentType;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function dispatch(Request $request)
    {
        try {

            if ($this->auth !== null && !$this->auth->authenticate($request))
                throw new StatusException(StatusCode::UNAUTHORIZED);

            if (!$this->router->requestMethodSupported($request->getRequestMethod()))
                throw new StatusException(StatusCode::METHOD_NOT_ALLOWED);

            $matches = $this->router->match($request->getRequestMethod(), $request->getRoute());

            $handler = $matches['exact'] ?: $matches['wildcard'];

            if (!$handler)
                throw new StatusException(StatusCode::NOT_FOUND);

            $container = new HandlerContainer($handler);

            try {

                set_error_handler(function($code, $message){
                    throw new \Exception($message, $code);
                }, E_ALL);

                $response = $container($request);

                restore_error_handler();

                $this->statusCode = StatusCode::OK;

            } catch (\Exception $exception) {

                if (!$exception instanceof StatusException)
                {
                    error_log($exception->getMessage() . ': ' . $exception->getTraceAsString());

                    $exception = new StatusException(StatusCode::INTERNAL_SERVER_ERROR, $exception);
                }

                throw $exception;
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
        $body = $this->getContentType() === 'application/json'
            ? json_encode(['status' => $code, 'message' => $message])
            : "<html lang='en'><head><title>$code $message</title></head><body><h1>$message</h1></body></html>";

        return new Response($this, $code, $body);
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