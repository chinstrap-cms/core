<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Middleware;

use Chinstrap\Core\Http\Middleware\HttpCacheMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class HttpCacheTest extends TestCase
{
    public function testNotGet()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('POST');
        $request->shouldReceive('getServerParams')->andReturn(['APP_ENV' => 'production']);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $middleware = new HttpCacheMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertEquals($received, $response);
    }

    public function testGet()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('GET');
        $request->shouldReceive('getServerParams')->andReturn(['APP_ENV' => 'production']);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('getBody->getContents')->andReturn('foo');
        $response->shouldReceive('withAddedHeader')->twice()->andReturn($response);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $middleware = new HttpCacheMiddleware();
        $received = $middleware->process($request, $handler);
    }

    public function testInactiveInDevelopment()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getMethod')->andReturn('POST');
        $request->shouldReceive('getServerParams')->andReturn(['APP_ENV' => 'development']);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $handler->shouldReceive('handle')->with($request)->andReturn($response);
        $middleware = new HttpCacheMiddleware();
        $received = $middleware->process($request, $handler);
        $this->assertEquals($received, $response);
    }
}
