<?php

declare(strict_types=1);

namespace Chinstrap\Core\Listeners;

use Chinstrap\Core\Http\Middleware\ETagMiddleware;
use Chinstrap\Core\Http\Middleware\HttpCacheMiddleware;
use Laminas\EventManager\EventInterface;
use League\Route\Router;

final class RegisterSystemDynamicRoutes extends BaseListener
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var HttpCacheMiddleware
     */
    private $cacheMiddleware;

    /**
     * @var ETagMiddleware
     */
    private $etagMiddleware;

    public function __construct(Router $router, HttpCacheMiddleware $cacheMiddleware, ETagMiddleware $etagMiddleware)
    {
        $this->router = $router;
        $this->cacheMiddleware = $cacheMiddleware;
        $this->etagMiddleware = $etagMiddleware;
    }

    public function __invoke(EventInterface $event): void
    {
        if ($_ENV['APP_ENV'] == 'development') {
            $this->router->get(
                '/__clockwork/{request:.+}',
                'Chinstrap\Core\Http\Controllers\ClockworkController::process'
            );
        }
        $this->router->get('/images/[{name}]', 'Chinstrap\Core\Http\Controllers\ImageController::get');
        $this->router->get('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::index')
               ->middleware($this->cacheMiddleware)
               ->middleware($this->etagMiddleware);
        $this->router->post('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::submit');
    }
}
