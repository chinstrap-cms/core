<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Chinstrap\Core\Contracts\Kernel\KernelInterface;
use Chinstrap\Core\Contracts\Plugin;
use Chinstrap\Core\Events\RegisterViewHelpers;
use Chinstrap\Core\Exceptions\Plugins\PluginNotFound;
use Chinstrap\Core\Exceptions\Plugins\PluginNotValid;
use Laminas\EventManager\EventManagerInterface;
use Psr\Container\ContainerInterface;
use PublishingKit\Config\Config;

/**
 * Application instance
 */
final class Kernel implements KernelInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

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
        /** @var class-string<Plugin> $name **/
        foreach ($plugins as $name) {
            if (!$plugin = $this->container->get($name)) {
                throw new PluginNotFound('Plugin could not be resolved by the container');
            }
            /** @var Plugin $plugin **/
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
