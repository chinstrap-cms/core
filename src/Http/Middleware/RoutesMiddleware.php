<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Chinstrap\Core\Contracts\Views\Renderer;
use League\Route\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RoutesMiddleware implements MiddlewareInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(Router $router, Renderer $renderer, ResponseInterface $response)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (getenv('APP_ENV') == 'development') {
            $this->router->get('/__clockwork/{request:.+}', 'Chinstrap\Core\Http\Controllers\ClockworkController::process');
        }
        $this->router->get('/images/[{name}]', 'Chinstrap\Core\Http\Controllers\ImageController::get');
        $this->router->get('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::index')
               ->middleware(new \Chinstrap\Core\Http\Middleware\HttpCache())
               ->middleware(new \Chinstrap\Core\Http\Middleware\ETag());
        $this->router->post('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::submit');
        try {
            $response = $this->router->dispatch($request);
        } catch (\League\Route\Http\Exception\NotFoundException $e) {
            $response = $this->renderer->render(
                $this->response->withStatus(404),
                '404.html'
            );
        }
        return $response;
    }
}
