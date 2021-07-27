<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Middleware;

use Chinstrap\Core\Http\Middleware\ClockworkMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Clockwork\Support\Vanilla\Clockwork;
use Mockery as m;

final class ClockworkMiddlewareTest extends TestCase
{
    public function testProcessInDev(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getServerParams')
                ->once()
                ->andReturn([
                    'APP_ENV' => 'development'
                ]);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $clockwork = $this->createStub(Clockwork::class);
        $clockwork->method('usePsrMessage')
                  ->willReturn($clockwork);
        $clockwork->method('requestProcessed')
            ->willReturn($response);
        $middleware = new ClockworkMiddleware($clockwork);
        $received = $middleware->process($request, $handler);
        $this->assertSame($response, $received);
    }

    public function testProcessInProduction(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getServerParams')
                ->once()
                ->andReturn([
                    'APP_ENV' => 'production'
                ]);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $clockwork = $this->createStub(Clockwork::class);
        $middleware = new ClockworkMiddleware($clockwork);
        $received = $middleware->process($request, $handler);
        $this->assertSame($response, $received);
    }
}
