<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Chinstrap\Core\Events\RegisterDynamicRoutes;
use Chinstrap\Core\Events\RegisterViewHelpers;
use Chinstrap\Core\Listeners\RegisterSystemDynamicRoutes;
use Chinstrap\Core\Listeners\RegisterViews;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

final class EventProvider extends AbstractServiceProvider
{
    protected $provides = [EventManagerInterface::class];

    public function register(): void
    {
        // Register items
        $container = $this->getContainer();
        $container->share(EventManagerInterface::class, function () use ($container) {
                $manager = $container->get(EventManager::class);
                $manager->attach(
                    RegisterDynamicRoutes::class,
                    $container->get(RegisterSystemDynamicRoutes::class)
                );
                $manager->attach(
                    RegisterViewHelpers::class,
                    $container->get(RegisterViews::class)
                );
                return $manager;
        });
    }
}
