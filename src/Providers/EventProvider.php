<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Chinstrap\Core\Events\RegisterDynamicRoutes;
use Chinstrap\Core\Events\RegisterViewHelpers;
use Chinstrap\Core\Listeners\RegisterSystemDynamicRoutes;
use Chinstrap\Core\Listeners\RegisterViews;
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
                $emitter->addListener(
                    RegisterDynamicRoutes::class,
                    $container->get(RegisterSystemDynamicRoutes::class)
                );
                $emitter->addListener(
                    RegisterViewHelpers::class,
                    $container->get(RegisterViews::class)
                );
                return $emitter;
        });
    }
}
