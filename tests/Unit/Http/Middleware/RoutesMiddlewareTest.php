<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Middleware;

use Chinstrap\Core\Events\RegisterDynamicRoutes;
use Chinstrap\Core\Events\RegisterStaticRoutes;
use Chinstrap\Core\Http\Middleware\RoutesMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Laminas\EventManager\EventManagerInterface;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use Mockery as m;

final class RoutesMiddlewareTest extends TestCase
{
    public function testDispatch(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $router = m::mock(Router::class);
        $router->shouldReceive('dispatch')
               ->with($request)
               ->once()
               ->andReturn($response);
        $eventManager = m::mock(EventManagerInterface::class);
        $eventManager->shouldReceive('trigger')
                     ->with(RegisterStaticRoutes::class)
                     ->once();
        $eventManager->shouldReceive('trigger')
                     ->with(RegisterDynamicRoutes::class)
                     ->once();
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $middleware = new RoutesMiddleware($router, $eventManager);
        $result = $middleware->process($request, $handler);
    }

    public function testNotFound(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $router = m::mock(Router::class);
        $router->shouldReceive('dispatch')
               ->with($request)
               ->once()
               ->andThrow(new NotFoundException('Not found'));
        $eventManager = m::mock(EventManagerInterface::class);
        $eventManager->shouldReceive('trigger')
                     ->with(RegisterStaticRoutes::class)
                     ->once();
        $eventManager->shouldReceive('trigger')
                     ->with(RegisterDynamicRoutes::class)
                     ->once();
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
                ->with($request)
            ->andReturn($response);
        $middleware = new RoutesMiddleware($router, $eventManager);
        $result = $middleware->process($request, $handler);
        $this->assertSame($result, $response);
    }
}
