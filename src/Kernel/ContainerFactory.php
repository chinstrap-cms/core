<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Chinstrap\Core\Contracts\Exceptions\Handler;
use Chinstrap\Core\Contracts\Factories\FormFactory;
use Chinstrap\Core\Contracts\Generators\Sitemap;
use Chinstrap\Core\Contracts\Sources\Source;
use Chinstrap\Core\Contracts\Views\Renderer;
use Chinstrap\Core\Events\RegisterDynamicRoutes;
use Chinstrap\Core\Events\RegisterViewHelpers;
use Chinstrap\Core\Exceptions\LogHandler;
use Chinstrap\Core\Factories\FlysystemFactory;
use Chinstrap\Core\Factories\Forms\LaminasFormFactory;
use Chinstrap\Core\Factories\MonologFactory;
use Chinstrap\Core\Generators\XmlStringSitemap;
use Chinstrap\Core\Listeners\RegisterSystemDynamicRoutes;
use Chinstrap\Core\Listeners\RegisterViews;
use Chinstrap\Core\Views\TwigRenderer;
use Clockwork\Support\Vanilla\Clockwork;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mail\Transport\InMemory;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\ServiceManager;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use League\Glide\Responses\PsrResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PublishingKit\Cache\Contracts\Factories\CacheFactory;
use PublishingKit\Cache\Contracts\Services\CacheContract;
use PublishingKit\Cache\Factories\StashCacheFactory;
use PublishingKit\Cache\Services\Cache\Psr6Cache;
use PublishingKit\Config\Config;
use Stash\Pool;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Whoops\Run;
use Whoops\RunInterface;

final class ContainerFactory
{
    public function __invoke(): ServiceManager
    {
        return new ServiceManager([
            'abstract_factories' => [
                ReflectionBasedAbstractFactory::class,
            ],
            'lazy_services' => [
                'classmap' => [
                    \Faker\Generator::class => \Faker\Generator::class,
                ],
            ],
            'aliases' => [
                Renderer::class => TwigRenderer::class,
                ContainerInterface::class => ServiceManager::class,
                Handler::class => LogHandler::class,
                Sitemap::class => XmlStringSitemap::class,
                TransportInterface::class => InMemory::class,
                FormFactory::class => LaminasFormFactory::class,
                CacheFactory::class => StashCacheFactory::class,
                CacheItemPoolInterface::class => Pool::class,
                ResponseInterface::class => Response::class,
                RunInterface::class => Run::class,
                'response' => Response::class,
            ],
            'factories' => [
                SessionMiddleware::class => function (
                    ContainerInterface $container
                ): SessionMiddleware {
                    $config = $container->get(Config::class);
                    return SessionMiddleware::fromSymmetricKeyDefaults(
                        $config->get('key'),
                        (int)$config->get('session_time')
                    );
                },
                \Faker\Generator::class => function (
                    ContainerInterface $container
): \Faker\Generator {
                    $config = $container->get(Config::class);
                    return \Faker\Factory::create($config->get('locale'));
                },
                Clockwork::class => function (): Clockwork {
                    return Clockwork::init();
                },
                Config::class => function (): Config {
                    return Config::fromFiles(glob(ROOT_DIR . 'config/*.*'));
                },
                FilesystemInterface::class => function (
                    ContainerInterface $container
): MountManager {
                    $factory = $container->get(FlysystemFactory::class);
                    $config = $container->get(Config::class);

                    // Decorate the adapter
                    $contentFilesystem = $factory->make($config->filesystem->content->toArray());
                    $assetFilesystem = $factory->make($config->filesystem->assets->toArray());
                    $mediaFilesystem = $factory->make($config->filesystem->media->toArray());
                    $cacheFilesystem = $factory->make($config->filesystem->cache->toArray());

                    return new MountManager([
                        'content' => $contentFilesystem,
                        'assets' => $assetFilesystem,
                        'media' => $mediaFilesystem,
                        'cache' => $cacheFilesystem,
                    ]);
                },
                Source::class => function (ContainerInterface $container): Source {
                    $config = $container->get(Config::class);
                    return $container->get($config->get('source'));
                },
                LoggerInterface::class => function (
                    ContainerInterface $container
): LoggerInterface {
                    /** @var Config **/
                    $config = $container->get('PublishingKit\Config\Config');
                    $factory = new MonologFactory();
                    return $factory->make($config->get('loggers'));
                },
                FilesystemLoader::class => function (
                    ContainerInterface $container
): FilesystemLoader {
                    return new FilesystemLoader(ROOT_DIR . 'resources' . DIRECTORY_SEPARATOR . 'views');
                },
                Environment::class => function (ContainerInterface $container): Environment {
                    $config = [];
                    if ($_ENV['APP_ENV'] !== 'development') {
                        $config['cache'] = ROOT_DIR . '/cache/views';
                    }
                    return new Environment($container->get('Twig\Loader\FilesystemLoader'), $config);
                },
                Router::class => function (ContainerInterface $container): Router {
                    $strategy = (new ApplicationStrategy())->setContainer($container);
                    $router = new Router();
                    $router->setStrategy($strategy);
                    return $router;
                },
                EventManagerInterface::class => function (
                    ContainerInterface $container
): EventManagerInterface {
                    /** @var EventManagerInterface **/
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
                },
                Server::class => function (ContainerInterface $container) {
                    $fs = $container->get('League\Flysystem\FilesystemInterface');
                    $source = $fs->getFilesystem('media');
                    $cache = $fs->getFilesystem('cache');
                    return ServerFactory::create([
                        'source' => $source,
                        'cache' => $cache,
                        'response' => new PsrResponseFactory(new Response(), function ($stream) {
                            return new Stream($stream);
                        }),
                    ]);
                },
                CacheContract::class => function (ContainerInterface $container): CacheContract {
                    return new Psr6Cache($container->get(CacheItemPoolInterface::class));
                },
                Pool::class => function (ContainerInterface $container): Pool {
                    $factory = $container->get(CacheFactory::class);
                    $config = $container->get(Config::class);
                    return $factory->make($config->cache->toArray());
                }
            ]
        ]);
    }
}
