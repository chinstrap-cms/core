<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Middleware;

use Chinstrap\Core\Contracts\Views\Renderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MaintenanceModeMiddleware implements MiddlewareInterface
{
    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(Renderer $renderer, ResponseInterface $response)
    {
        $this->renderer = $renderer;
        $this->response = $response;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $env = $request->getServerParams();
        if (isset($env['MAINTENANCE'])) {
            return $this->renderer->render(
                $this->response->withStatus(503)->withAddedHeader('Retry-After', $env['MAINTENANCE']),
                'maintenance.html'
            );
        }
        return $handler->handle($request);
    }
}
