<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Chinstrap\Core\Contracts\Kernel\KernelInterface;
use Chinstrap\Core\Events\RegisterViewHelpers;
use Chinstrap\Core\Exceptions\Plugins\PluginNotFound;
use Chinstrap\Core\Exceptions\Plugins\PluginNotValid;
use Chinstrap\Core\Kernel\Kernel;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\EventManager\EventManagerInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application instance
 */
final class Kernel implements KernelInterface
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container = null)
    {
        if (!$container) {
            $container = new Container();
        }
        $this->container = $container;
    }

    /**
     * Bootstrap the application
     *
     * @return void
     */
    public function bootstrap(): void
    {
        $this->setupContainer();
        $this->setupPlugins();
        $this->registerViewHelpers();
    }

    private function setupContainer(): void
    {
        $container = $this->container;
        $container->delegate(
            new ReflectionContainer()
        );

        $container->share('response', \Laminas\Diactoros\Response::class);
        $container->share('Psr\Http\Message\ResponseInterface', \Laminas\Diactoros\Response::class);
        $this->container = $container;
    }

    private function setupPlugins(): void
    {
        $config = $this->container->get('PublishingKit\Config\Config');
        if (!$plugins = $config->get('plugins')) {
            return;
        }
        foreach ($plugins as $name) {
            if (!$plugin = $this->container->get($name)) {
                throw new PluginNotFound('Plugin could not be resolved by the container');
            }
            if (!in_array('Chinstrap\Core\Contracts\Plugin', array_keys(class_implements($name)))) {
                throw new PluginNotValid('Plugin does not implement Chinstrap\Core\Contracts\Plugin');
            }
            $plugin->register();
        }
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    private function setupBaseProviders()
    {
        foreach ($this->baseProviders as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }

    private function setupAdditionalProvideers()
    {
        foreach ($this->additionalProviders as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }

    private function registerViewHelpers()
    {
        $manager = $this->container->get(EventManagerInterface::class);
        $manager->trigger(RegisterViewHelpers::class);
    }
}
