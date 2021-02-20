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
use Whoops\RunInterface;

final class WhoopsMiddleware implements MiddlewareInterface
{
    /**
     * @var RunInterface
     */
    private $whoops;
    /**
     * @var Handler
     */
    private $errorHandler;

    public function __construct(RunInterface $whoops, Handler $errorHandler)
    {
        $this->whoops = $whoops;
        $this->errorHandler = $errorHandler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $env = $request->getServerParams();
        if ($env['APP_ENV'] === 'production') {
            $this->whoops->prependHandler(new CallbackHandler($this->errorHandler));
        } else {
            $this->whoops->prependHandler(new PrettyPageHandler());
        }
        $this->whoops->register();
        return $handler->handle($request);
    }
}
