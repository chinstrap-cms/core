<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Chinstrap\Core\Contracts\Exceptions\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class WhoopsMiddleware implements MiddlewareInterface
{
    /**
     * @var Run
     */
    private $whoops;
    /**
     * @var Handler
     */
    private $handler;

    public function __construct(Run $whoops, Handler $handler)
    {
        $this->whoops = $whoops;
        $this->handler = $handler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($_ENV['APP_ENV'] === 'production') {
            $this->whoops->prependHandler(new CallbackHandler($this->handler));
        } else {
            $this->whoops->prependHandler(new PrettyPageHandler());
        }
        $this->whoops->register();
        return $this->handler->handle($request);
    }
}
