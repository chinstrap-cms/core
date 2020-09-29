<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment;

final class TwigProvider extends AbstractServiceProvider
{
    protected $provides = [
                           'Twig\Environment',
                          ];

    public function register(): void
    {
        // Register items
        $container = $this->getContainer();
        $container->share('Twig\Environment', function () use ($container) {
            $config = [];
            if ($_ENV['APP_ENV'] !== 'development') {
                $config['cache'] = ROOT_DIR . '/cache/views';
            }
            return new Environment($container->get('Twig\Loader\FilesystemLoader'), $config);
        });
    }
}
