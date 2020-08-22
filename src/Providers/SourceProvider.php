<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Chinstrap\Core\Sources\Decorators\Psr6CacheDecorator;

final class SourceProvider extends AbstractServiceProvider
{
    protected $provides = ['Chinstrap\Core\Contracts\Sources\Source'];

    public function register(): void
    {
        // Register items
        $container = $this->getContainer();
        $config = $container->get('PublishingKit\Config\Config');
        $container->add('Chinstrap\Core\Contracts\Sources\Source', function () use ($config, $container) {
            return $container->get($config->get('source'));
        });
    }
}
