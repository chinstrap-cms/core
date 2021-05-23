<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Laminas\Diactoros\StreamFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PublishingKit\HttpProxy\Client;
use PublishingKit\HttpProxy\Proxy;

final class CachingRequestHandlerDecorator implements RequestHandlerInterface
{
    private RequestHandlerInterface $handler;

    private CacheItemPoolInterface $cache;

    public function __construct(RequestHandlerInterface $handler, CacheItemPoolInterface $cache)
    {
        $this->handler = $handler;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $client = new Client(function (ServerRequestInterface $request): ResponseInterface {
            return $this->handler->handle($request);
        });
        $proxy = new Proxy($client, $this->cache, new StreamFactory());
        return $proxy->handle($request);
    }
}
