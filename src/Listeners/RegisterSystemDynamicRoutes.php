<?php

declare(strict_types=1);

namespace Chinstrap\Core\Listeners;

use Chinstrap\Core\Http\Handlers\ClockworkHandler;
use Chinstrap\Core\Http\Handlers\ImageHandler;
use Chinstrap\Core\Http\Handlers\PageHandler;
use Chinstrap\Core\Http\Handlers\SubmissionHandler;
use Chinstrap\Core\Http\Middleware\ClockworkMiddleware;
use Chinstrap\Core\Http\Middleware\ETagMiddleware;
use Chinstrap\Core\Http\Middleware\HttpCacheMiddleware;
use Laminas\EventManager\EventInterface;
use League\Route\Router;

final class RegisterSystemDynamicRoutes extends BaseListener
{
    private Router $router;

    private HttpCacheMiddleware $cacheMiddleware;

    private ETagMiddleware $etagMiddleware;

    private ClockworkMiddleware $clockworkMiddleware;

    public function __construct(
        Router $router,
        HttpCacheMiddleware $cacheMiddleware,
        ETagMiddleware $etagMiddleware,
        ClockworkMiddleware $clockworkMiddleware
    ) {
        $this->router = $router;
        $this->cacheMiddleware = $cacheMiddleware;
        $this->etagMiddleware = $etagMiddleware;
        $this->clockworkMiddleware = $clockworkMiddleware;
    }

    public function __invoke(EventInterface $event): void
    {
        if ($_ENV['APP_ENV'] === 'development') {
            $this->router->get(
                '/__clockwork/{request:.+}',
                ClockworkHandler::class
            );
        }
        $this->router->get('/images/[{name}]', ImageHandler::class);
        $this->router->get('/[{name:[a-zA-Z0-9\-\/]+}]', PageHandler::class)
               ->middleware($this->cacheMiddleware)
               ->middleware($this->etagMiddleware);
        $this->router->post('/[{name:[a-zA-Z0-9\-\/]+}]', SubmissionHandler::class);
    }
}
