<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment;

final class TwigProvider extends AbstractServiceProvider
{
    protected $provides = [
                           'Twig\Environment',
                           'Chinstrap\Core\Contracts\Services\Navigator',
                          ];

    public function register(): void
    {
        // Register items
        $container = $this->getContainer();
        $container->add('Chinstrap\Core\Contracts\Services\Navigator', function () use ($container) {
            return $container->get('Chinstrap\Core\Services\Navigation\DynamicNavigator');
        });
        $container->share('Twig\Environment', function () use ($container) {
            $config = [];
            if (getenv('APP_ENV') !== 'development') {
                $config['cache'] = ROOT_DIR . '/cache/views';
            }
            return new Environment($container->get('Twig\Loader\FilesystemLoader'), $config);
        });
    }
}
