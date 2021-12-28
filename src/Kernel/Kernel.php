<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Chinstrap\Core\Contracts\Kernel\KernelInterface;
use Chinstrap\Core\Contracts\Plugin;
use Chinstrap\Core\Events\RegisterViewHelpers;
use Chinstrap\Core\Exceptions\Plugins\PluginNotFound;
use Chinstrap\Core\Exceptions\Plugins\PluginNotValid;
use Laminas\EventManager\EventManagerInterface;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Psr\Container\ContainerInterface;
use PublishingKit\Config\Config;

/**
 * Application instance
 */
final class Kernel implements KernelInterface
{
    private ContainerInterface $container;

    private EventManagerInterface $eventManager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->eventManager = $container->get(EventManagerInterface::class);
    }

    /**
     * Bootstrap the application
     *
     * @return void
     */
    public function bootstrap(): void
    {
        $this->setupPlugins();
        $this->registerViewHelpers();
    }

    private function setupPlugins(): void
    {
        $config = $this->container->get(Config::class);
        if (!$plugins = $config->get('plugins')) {
            return;
        }
        /** @var array<class-string<Plugin>> $plugins **/
        foreach ($plugins as $name) {
            try {
                $plugin = $this->container->get($name);
            } catch (ServiceNotFoundException $e) {
                throw new PluginNotFound('Plugin could not be resolved by the container');
            }
            if (!in_array(Plugin::class, array_keys(class_implements($name)))) {
                throw new PluginNotValid('Plugin does not implement ' . Plugin::class);
            }
            $plugin->register();
        }
    }

    private function registerViewHelpers(): void
    {
        $this->eventManager->trigger(RegisterViewHelpers::class);
    }
}
