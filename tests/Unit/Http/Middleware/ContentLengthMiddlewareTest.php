<?php

declare(strict_types=1);

namespace Middlewares\Tests;

use Chinstrap\Core\Http\Middleware\ContentLengthMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class ContentLengthMiddlewareTest extends TestCase
{
    public function testWithoutContentLength(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')
                 ->with('Content-Length')
                 ->once()
                ->andReturn(false);
        $response->shouldReceive('withHeader')
                 ->with('Content-Length', 5)
                 ->andReturn($response);
        $response->shouldReceive('getBody->getSize')
                 ->once()
                 ->andReturn(5);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
                ->once()
                ->andReturn($response);
        $middleware = new ContentLengthMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertSame($received, $response);
    }

    public function testWithContentLength(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')
                 ->with('Content-Length')
                 ->once()
                ->andReturn(true);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
               ->once()
               ->andReturn($response);
        $middleware = new ContentLengthMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertSame($received, $response);
    }

    public function testEmptyContentLength(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')
                 ->with('Content-Length')
                 ->once()
                ->andReturn(false);
        $response->shouldReceive('withHeader')
                 ->with('Content-Length', 0)
                 ->andReturn($response);
        $response->shouldReceive('getBody->getSize')
                 ->once()
                 ->andReturn(0);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
                ->once()
                ->andReturn($response);
        $middleware = new ContentLengthMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertSame($received, $response);
    }

    public function testNullContentLength(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')
                 ->with('Content-Length')
                 ->once()
                 ->andReturn(false);
        $response->shouldNotReceive('withHeader');
        $response->shouldReceive('getBody->getSize')
                 ->once()
                 ->andReturn(null);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')
                ->once()
                ->andReturn($response);
        $middleware = new ContentLengthMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertSame($received, $response);
    }
}
