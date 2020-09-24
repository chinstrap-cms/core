<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Laminas\Diactoros\ServerRequestFactory;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Http\Message\ServerRequestInterface;
use Chinstrap\Core\Kernel\Kernel;
use Chinstrap\Core\Exceptions\Plugins\PluginNotFound;
use Chinstrap\Core\Exceptions\Plugins\PluginNotValid;
use Chinstrap\Core\Contracts\Kernel\KernelInterface;

/**
 * Application instance
 */
final class Kernel implements KernelInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $providers = [
                          'Chinstrap\Core\Providers\ContainerProvider',
                          'Chinstrap\Core\Providers\CacheProvider',
                          'Chinstrap\Core\Providers\ClockworkProvider',
                          'Chinstrap\Core\Providers\ConfigProvider',
                          'Chinstrap\Core\Providers\ConsoleProvider',
                          'Chinstrap\Core\Providers\EventProvider',
                          'Chinstrap\Core\Providers\FlysystemProvider',
                          'Chinstrap\Core\Providers\FormsProvider',
                          'Chinstrap\Core\Providers\HandlerProvider',
                          'Chinstrap\Core\Providers\LoggerProvider',
                          'Chinstrap\Core\Providers\RouterProvider',
                          'Chinstrap\Core\Providers\SessionProvider',
                          'Chinstrap\Core\Providers\SitemapGeneratorProvider',
                          'Chinstrap\Core\Providers\SourceProvider',
                          'Chinstrap\Core\Providers\TwigProvider',
                          'Chinstrap\Core\Providers\TwigLoaderProvider',
                          'Chinstrap\Core\Providers\ViewProvider',
                          'Chinstrap\Core\Providers\YamlProvider',
                          'Chinstrap\Core\Providers\MailerProvider',
                          'Chinstrap\Core\Providers\GlideProvider',
                         ];

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
        $this->setErrorHandler();
        $this->setupPlugins();
    }

    private function setupContainer(): void
    {
        $container = $this->container;
        $container->delegate(
            new ReflectionContainer()
        );

        foreach ($this->providers as $provider) {
            $container->addServiceProvider($provider);
        }
        $container->share('response', \Laminas\Diactoros\Response::class);
        $container->share('Psr\Http\Message\ResponseInterface', \Laminas\Diactoros\Response::class);
        $this->container = $container;
    }

    private function setErrorHandler(): void
    {
        error_reporting(E_ALL);
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
}
