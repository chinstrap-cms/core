<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PublishingKit\Csrf\Token;
use PublishingKit\Csrf\TokenStorage;

final class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(TokenStorage $storage)
    {
        $this->storage = $storage;
        $this->key = 'foo';
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            $response = $handler->handle($request);
            $token = Token::generate();
            $this->storage->store($this->key, $token);
            return $response->withAddedHeader('X-CSRF-TOKEN', $token->__toString());
        } else {
            $token = $this->storage->retrieve($this->key);
            return $handler->handle($request);
        }
    }
}
