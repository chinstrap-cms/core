<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

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

    public function __construct(Router $router)
    {
        $this->router = $router;
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
