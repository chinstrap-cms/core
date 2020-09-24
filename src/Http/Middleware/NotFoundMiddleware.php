<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class NotFoundMiddleware implements MiddlewareInterface
{
    public function __construct(Renderer $renderer, ResponseInterface $response)
    {
        $this->renderer = $renderer;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->renderer->render(
            $this->response->withStatus(404),
            '404.html'
        );
    }
}
