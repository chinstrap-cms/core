<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PublishingKit\Csrf\Token;

final class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            $response = $handler->handle($request);
            return $response->withAddedHeader('X-CSRF-TOKEN', Token::generate()->__toString());
        } else {
            return $handler->handle($request);
        }
    }
}
