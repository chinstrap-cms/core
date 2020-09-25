<?php

declare(strict_types=1);

namespace Chinstrap\Core\Listeners;

use Chinstrap\Core\Http\Middleware\ETag;
use Chinstrap\Core\Http\Middleware\HttpCache;
use League\Event\EventInterface;
use League\Route\Router;

final class RegisterAllStaticRoutes extends BaseListener
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var HttpCache
     */
    private $cacheMiddleware;

    /**
     * @var ETag
     */
    private $etagMiddleware;

    public function __construct(Router $router, HttpCache $cacheMiddleware, ETag $etagMiddleware)
    {
        $this->router = $router;
        $this->cacheMiddleware = $cacheMiddleware;
        $this->etagMiddleware = $etagMiddleware;
    }

    public function handle(EventInterface $event)
    {
        if (getenv('APP_ENV') == 'development') {
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
