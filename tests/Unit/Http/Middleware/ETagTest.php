<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Middleware;

use Chinstrap\Core\Http\Middleware\ETagMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class ETagTest extends TestCase
{
    public function testNotGet(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('POST');
        $request->shouldReceive('getServerParams')->andReturn(['APP_ENV' => 'production']);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $middleware = new ETagMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertEquals($received, $response);
    }

    public function testGet(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('GET');
        $request->shouldReceive('getServerParams')->andReturn(['APP_ENV' => 'production']);
        $request->shouldReceive('hasHeader')->with('if-none-match')->andReturn(true);
        $request->shouldReceive('getHeader')->with('if-none-match')->andReturn([md5('foo')]);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody->getContents')->andReturn('foo');
        $response->shouldReceive('withAddedHeader')->andReturn($response);
        $response->shouldReceive('withStatus')->with(304)->andReturn($response);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $middleware = new ETagMiddleware();
        $received = $middleware->process($request, $handler);
    }

    public function testInactiveInDevelopment(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('POST');
        $request->shouldReceive('getServerParams')->andReturn(['APP_ENV' => 'development']);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $middleware = new ETagMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertEquals($received, $response);
    }

    public function testGetWithoutExistingEtag(): void
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('GET');
        $request->shouldReceive('getServerParams')->andReturn(['APP_ENV' => 'production']);
        $request->shouldReceive('hasHeader')->with('if-none-match')->andReturn(false);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody->getContents')->andReturn('foo');
        $response->shouldReceive('withAddedHeader')->andReturn($response);
        $response->shouldReceive('withStatus')->with(304)->andReturn($response);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $middleware = new ETagMiddleware();
        $received = $middleware->process($request, $handler);
    }
}
