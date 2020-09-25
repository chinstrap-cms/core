<?php

declare(strict_types=1);

namespace Chinstrap\Core\Listeners;

use League\Event\EventInterface;
use League\Route\Router;

final class RegisterAllDynamicRoutes extends BaseListener
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function handle(EventInterface $event)
    {
    }
}
