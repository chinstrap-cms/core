<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class WhoopsMiddleware implements MiddlewareInterface
{
    public function __construct(Run $whoops)
    {
        $this->whoops = $whoops;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (getenv('APP_ENV') === 'production') {
            $handler = $this->container->get('Chinstrap\Core\Contracts\Exceptions\Handler');
            $this->whoops->prependHandler(new CallbackHandler($handler));
        } else {
            $this->whoops->prependHandler(new PrettyPageHandler());
        }
        $this->whoops->register();
        return $handler->handle($request);
    }
}
