<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Laminas\Diactoros\ServerRequestFactory;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Http\Message\ServerRequestInterface;
use Chinstrap\Core\Kernel\Application;
use Chinstrap\Core\Exceptions\Plugins\PluginNotFound;
use Chinstrap\Core\Exceptions\Plugins\PluginNotValid;
use Chinstrap\Core\Contracts\Kernel\KernelInterface;

/**
 * Application instance
 */
final class Application implements KernelInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var \League\Route\Router|null
     */
    private $router;

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
     * @return Application
     */
    public function bootstrap(): Application
    {
        $this->setupContainer();
        $this->setErrorHandler();
        $this->setupPlugins();
        $this->setupRoutes();
        return $this;
    }

    /**
     * Handle a request
     *
     * @param ServerRequestInterface $request HTTP request.
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        if (!isset($this->router)) {
            throw new Exception('Router not set');
        }
        try {
            $response = $this->router->dispatch($request);
        } catch (\League\Route\Http\Exception\NotFoundException $e) {
            $view = $this->container->get('Chinstrap\Core\Contracts\Views\Renderer');
            $response = $view->render(
                $this->container->get('response')->withStatus(404),
                '404.html'
            );
        }
        if (getenv('APP_ENV') == 'development') {
            $clockwork = $this->container->get('Clockwork\Support\Vanilla\Clockwork');
            $clockwork->requestProcessed();
        }
        return $response;
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
        $environment = getenv('APP_ENV');

        $whoops = new \Whoops\Run();
        if ($environment !== 'production') {
            $whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler());
        } else {
            $handler = $this->container->get('Chinstrap\Core\Contracts\Exceptions\Handler');
            $whoops->prependHandler(new \Whoops\Handler\CallbackHandler($handler));
        }
        $whoops->register();
    }

    private function setupRoutes(): void
    {
        $router = $this->container->get('League\Route\Router');
        if (getenv('APP_ENV') == 'development') {
            $router->get('/__clockwork/{request:.+}', 'Chinstrap\Core\Http\Controllers\ClockworkController::process');
        }
        $router->get('/images/[{name}]', 'Chinstrap\Core\Http\Controllers\ImageController::get');
        $router->get('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::index')
               ->middleware(new \Chinstrap\Core\Http\Middleware\HttpCache())
               ->middleware(new \Chinstrap\Core\Http\Middleware\ETag());
        $router->post('/[{name:[a-zA-Z0-9\-\/]+}]', 'Chinstrap\Core\Http\Controllers\MainController::submit');
        $this->router = $router;
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
