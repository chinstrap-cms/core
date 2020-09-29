<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Chinstrap\Core\Events\RegisterDynamicRoutes;
use Chinstrap\Core\Events\RegisterStaticRoutes;
use Laminas\EventManager\EventManagerInterface;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RoutesMiddleware implements MiddlewareInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    public function __construct(
        Router $router,
        EventManagerInterface $eventManager
    ) {
        $this->router = $router;
        $this->eventManager = $eventManager;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->eventManager->trigger(RegisterStaticRoutes::class);
        $this->eventManager->trigger(RegisterDynamicRoutes::class);
        try {
            return $this->router->dispatch($request);
        } catch (NotFoundException $e) {
            return $handler->handle($request);
        }
    }
}
