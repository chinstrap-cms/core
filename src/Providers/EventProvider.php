<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Chinstrap\Core\Events\RegisterDynamicRoutes;
use Chinstrap\Core\Events\RegisterStaticRoutes;
use Chinstrap\Core\Listeners\RegisterAllDynamicRoutes;
use Chinstrap\Core\Listeners\RegisterAllStaticRoutes;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Event\Emitter;

final class EventProvider extends AbstractServiceProvider
{
    protected $provides = ['League\Event\EmitterInterface'];

    public function register(): void
    {
        // Register items
        $container = $this->getContainer();
        $container->share('League\Event\EmitterInterface', function () use ($container) {
                $emitter = $container->get('League\Event\Emitter');
                $emitter->addListener(RegisterStaticRoutes::class, $container->get(RegisterAllStaticRoutes::class));
                $emitter->addListener(RegisterDynamicRoutes::class, $container->get(RegisterAllDynamicRoutes::class));
                return $emitter;
        });
    }
}
