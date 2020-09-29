<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Chinstrap\Core\Contracts\Exceptions\Handler;
use Chinstrap\Core\Contracts\Generators\Sitemap;
use Chinstrap\Core\Contracts\Sources\Source;
use Chinstrap\Core\Contracts\Views\Renderer;
use Chinstrap\Core\Exceptions\LogHandler;
use Chinstrap\Core\Generators\XmlStringSitemap;
use Chinstrap\Core\Views\TwigRenderer;
use Clockwork\Support\Vanilla\Clockwork;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\ServiceManager;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Psr\Container\ContainerInterface;
use PublishingKit\Config\Config;

final class ContainerFactory
{
    public function __invoke(): ServiceManager
    {
        return new ServiceManager([
            'abstract_factories' => [
                ReflectionBasedAbstractFactory::class,
            ],
            'aliases' => [
                Renderer::class => TwigRenderer::class,
                ContainerInterface::class => ServiceManager::class,
                Handler::class => LogHandler::class,
                Sitemap::class => XmlStringSitemap::class,
            ],
            'factories' => [
                Clockwork::class => function (ContainerInterface $container, string $requestedName) {
                    return Clockwork::init();
                },
                Config::class => function (ContainerInterface $container, string $requestedName) {
                    return Config::fromFiles(glob(ROOT_DIR . 'config/*.*'));
                },
                Filesystem::class => function (ContainerInterface $container, string $requestedName) {
                    $factory = $container->get('Chinstrap\Core\Factories\FlysystemFactory');
                    $config = $container->get('PublishingKit\Config\Config');

                    // Decorate the adapter
                    $contentFilesystem = $factory->make($config->filesystem->content->toArray());
                    $assetFilesystem = $factory->make($config->filesystem->assets->toArray());
                    $mediaFilesystem = $factory->make($config->filesystem->media->toArray());
                    $cacheFilesystem = $factory->make($config->filesystem->cache->toArray());

                    return new MountManager([
                        'content' => $contentFilesystem,
                        'assets'  => $assetFilesystem,
                        'media'   => $mediaFilesystem,
                        'cache'   => $cacheFilesystem,
                    ]);
                },
                Source::class => function (ContainerInterface $container, string $requestedName) {
                    $config = $container->get(Config::class);
                    return $container->get($config->get('source'));
                }
            ]
        ]);
    }
}
