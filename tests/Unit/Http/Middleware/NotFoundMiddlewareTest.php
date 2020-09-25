<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Middleware;

use Chinstrap\Core\Http\Middleware\NotFoundMiddleware;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;

final class NotFoundMiddlewareTest extends TestCase
{
    public function testRun()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('withStatus')->with(404)->andReturn($response);
        $handler = m::mock('Psr\Http\Server\RequestHandlerInterface');
        $renderer = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $renderer->shouldReceive('render')->with($response, '404.html');
        $middleware = new NotFoundMiddleware($renderer, $response);
        $received = $middleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $received);
    }
}
