<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Chinstrap\Core\Http\Middleware\ClockworkMiddleware;
use Chinstrap\Core\Http\Middleware\ContentLengthMiddleware;
use Chinstrap\Core\Http\Middleware\CsrfMiddleware;
use Chinstrap\Core\Http\Middleware\NotFoundMiddleware;
use Chinstrap\Core\Http\Middleware\RoutesMiddleware;
use Chinstrap\Core\Http\Middleware\WhoopsMiddleware;
use Laminas\Stratigility\MiddlewarePipe;

final class AppFactory
{
    public function __invoke(): MiddlewarePipe
    {
        $app = new MiddlewarePipe();

        $kernel = new Kernel();
        $kernel->bootstrap();
        $container = $kernel->getContainer();

        $app->pipe($container->get(WhoopsMiddleware::class));
        $app->pipe($container->get(ClockworkMiddleware::class));
        $app->pipe($container->get(ContentLengthMiddleware::class));
        $app->pipe($container->get(CsrfMiddleware::class));
        $app->pipe($container->get(RoutesMiddleware::class));
        $app->pipe($container->get(NotFoundMiddleware::class));
        return $app;
    }
}
