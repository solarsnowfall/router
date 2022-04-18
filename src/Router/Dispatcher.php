<?php

namespace Solar\Router;

use Solar\Auth\AuthInterface;
use Solar\Curl\Response;
use Solar\Http\StatusCode;
use Solar\Http\StatusException;
use Solar\Router\Response\GenericResponse;
use Solar\Router\Response\JsonResponse;
use Solar\Router\Response\ResponseInterface;

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

                if (!$response instanceof ResponseInterface)
                    $response = $this->newResponse(StatusCode::OK, $response);

                restore_error_handler();

                $this->statusCode = $response->getStatus();

            } catch (\Exception $exception) {

                if (!$exception instanceof StatusException)
                {
                    error_log($exception->getMessage() . ': ' . $exception->getTraceAsString());

                    $exception = new StatusException(StatusCode::INTERNAL_SERVER_ERROR, $exception);
                }

                throw $exception;
            }

        } catch (\Exception $exception) {

            if (!isset($response) || !$response instanceof ResponseInterface)
                $response = $this->newResponse($exception->getCode(), $exception->getMessage());

            $this->statusCode = $response->getStatus();

        } finally {

            $this->sendHeaders();

            echo $response;
        }
    }

    /**
     * @param int $code
     * @param string $message
     * @return ResponseInterface
     */
    protected function newResponse(int $code, string $message): ResponseInterface
    {
        if ($this->getContentType() === 'application/json')
            return new JsonResponse($code, $message);

        return new GenericResponse($code, $message);
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