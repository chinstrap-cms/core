<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Chinstrap\Core\Http\Middleware\ETag;
use Chinstrap\Core\Http\Middleware\HttpCache;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

final class RouterProvider extends AbstractServiceProvider
{
    protected $provides = ['League\Route\Router'];

    public function register(): void
    {
        // Register items
        $this->getContainer()
            ->share('League\Route\Router', function () {
                $strategy = (new ApplicationStrategy())->setContainer($this->getContainer());
                $router = new Router();
                $router->setStrategy($strategy);
                if (getenv('APP_ENV') == 'development') {
                    $router->get('/__clockwork/{request:.+}', 'Chinstrap\Core\Http\Controllers\ClockworkController::process');
                }
                $router->get('/images/[{name}]', 'Chinstrap\Core\Http\Controllers\ImageController::get');
                $router->get('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::index')
                             ->middleware(new HttpCache())
                             ->middleware(new ETag());
                $router->post('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::submit');
                return $router;
            });
    }
}
