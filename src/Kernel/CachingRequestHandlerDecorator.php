<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Http\Message\StreamFactory\DiactorosStreamFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PublishingKit\HttpProxy\Client;
use PublishingKit\HttpProxy\Proxy;

final class CachingRequestHandlerDecorator implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

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
        $client = new Client(function ($request) {
            return $this->handler->handle($request);
        });
        $proxy = new Proxy($client, $this->cache, new DiactorosStreamFactory());
        return $proxy->handle($request);
    }
}
