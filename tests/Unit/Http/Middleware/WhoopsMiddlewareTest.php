<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Middleware;

use Chinstrap\Core\Contracts\Exceptions\Handler;
use Chinstrap\Core\Http\Middleware\WhoopsMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\RunInterface;

final class WhoopsMiddlewareTest extends TestCase
{
    public function testDevelopment(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getServerParams')
                ->once()
                ->andReturn([
                    'APP_ENV' => 'development'
                ]);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
            ->andReturn($response);
        $whoops = m::mock(RunInterface::class);
        $whoops->shouldReceive('prependHandler')->once()->with(m::type(PrettyPageHandler::class));
        $whoops->shouldReceive('register')->once();
        $errorHandler = m::mock(Handler::class);
        $middleware = new WhoopsMiddleware($whoops, $errorHandler);
        $received = $middleware->process($request, $handler);
        $this->assertSame($response, $received);
    }

    public function testProduction(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getServerParams')
                ->once()
                ->andReturn([
                    'APP_ENV' => 'production'
                ]);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
            ->andReturn($response);
        $whoops = m::mock(RunInterface::class);
        $whoops->shouldReceive('prependHandler')->once()->with(m::type(CallbackHandler::class));
        $whoops->shouldReceive('register')->once();
        $errorHandler = m::mock(Handler::class);
        $middleware = new WhoopsMiddleware($whoops, $errorHandler);
        $received = $middleware->process($request, $handler);
        $this->assertSame($response, $received);
    }
}
