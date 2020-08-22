<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Chinstrap\Core\Views\Filters\Mix;
use Chinstrap\Core\Views\Filters\Version;
use Chinstrap\Core\Views\Functions\Form;

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
        $container->add('Twig\Environment', function () use ($container) {
            $version = $container->get('Chinstrap\Core\Views\Filters\Version');
            $mix = $container->get('Chinstrap\Core\Views\Filters\Mix');
            $config = [];
            if (getenv('APP_ENV') !== 'development') {
                $config['cache'] = ROOT_DIR . '/cache/views';
            }

            $twig = new Environment($container->get('Twig\Loader\FilesystemLoader'), $config);
            $twig->addFilter(new TwigFilter('version', $version));
            $twig->addFilter(new TwigFilter('mix', $mix));
            $twig->addFunction(new TwigFunction(
                'form',
                $container->get('Chinstrap\Core\Views\Functions\Form')
            ));
            $twig->addGlobal('navigation', $container->get('Chinstrap\Core\Contracts\Services\Navigator')->__invoke());

            return $twig;
        });
    }
}
