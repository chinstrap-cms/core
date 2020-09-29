<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Chinstrap\Core\Http\Middleware\ClockworkMiddleware;
use Chinstrap\Core\Http\Middleware\ContentLengthMiddleware;
use Chinstrap\Core\Http\Middleware\NotFoundMiddleware;
use Chinstrap\Core\Http\Middleware\RoutesMiddleware;
use Chinstrap\Core\Http\Middleware\WhoopsMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Container\ContainerInterface;

final class AppFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(): MiddlewarePipe
    {
        $app = new MiddlewarePipe();
        $kernel = new Kernel($this->container);
        $kernel->bootstrap();

        $app->pipe($this->container->get(WhoopsMiddleware::class));
        $app->pipe($this->container->get(ClockworkMiddleware::class));
        $app->pipe($this->container->get(ContentLengthMiddleware::class));
        $app->pipe($this->container->get(RoutesMiddleware::class));
        $app->pipe($this->container->get(NotFoundMiddleware::class));
        return $app;
    }
}
