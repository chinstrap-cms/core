<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Chinstrap\Core\Events\RegisterDynamicRoutes;
use Chinstrap\Core\Events\RegisterStaticRoutes;
use League\Event\EmitterInterface;
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

    public function __construct(Router $router, EmitterInterface $emitter)
    {
        $this->router = $router;
        $this->emitter = $emitter;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->emitter->emit(new RegisterStaticRoutes());
        $this->emitter->emit(new RegisterDynamicRoutes());
        try {
            return $this->router->dispatch($request);
        } catch (NotFoundException $e) {
            return $handler->handle($request);
        }
    }
}
