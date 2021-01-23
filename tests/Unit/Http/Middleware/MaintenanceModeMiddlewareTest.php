<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Middleware;

use Chinstrap\Core\Http\Middleware\MaintenanceModeMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;

final class MaintenanceModeMiddlewareTest extends TestCase
{
    public function testActive(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getServerParams')
                ->once()
                ->andReturn([
                    'MAINTENANCE' => 600
                ]);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('withStatus')->with(503)->andReturn($response);
        $response->shouldReceive('withAddedHeader')->with('Retry-After', 600)->andReturn($response);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldNotReceive('handle');
        $renderer = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $renderer->shouldReceive('render')->with($response, 'maintenance.html');
        $middleware = new MaintenanceModeMiddleware($renderer, $response);
        $received = $middleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $received);
    }

    public function testInactive(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getServerParams')
                ->once()
                ->andReturn([
                ]);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
            ->andReturn($response);
        $renderer = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $middleware = new MaintenanceModeMiddleware($renderer, $response);
        $received = $middleware->process($request, $handler);
        $this->assertSame($response, $received);
    }
}
