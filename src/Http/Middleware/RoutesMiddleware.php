<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Chinstrap\Core\Contracts\Views\Renderer;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RoutesMiddleware implements MiddlewareInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(Router $router, Renderer $renderer, ResponseInterface $response)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->router->dispatch($request);
        } catch (NotFoundException $e) {
            return $handler->handle($request);
        }
    }
}
