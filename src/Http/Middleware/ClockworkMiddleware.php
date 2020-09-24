<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Clockwork\Support\Vanilla\Clockwork;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ClockworkMiddleware implements MiddlewareInterface
{
    /**
     * @var Clockwork
     */
    private $clockwork;

    public function __construct(Clockwork $clockwork)
    {
        $this->clockwork = $clockwork;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (getenv('APP_ENV') == 'development') {
            return $this->clockwork->usePsrMessage($request, $response)->requestProcessed();
        }
        return $response;
    }
}
