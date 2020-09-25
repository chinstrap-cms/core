<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Chinstrap\Core\Views\TwigRenderer;
use League\Container\ServiceProvider\AbstractServiceProvider;

final class ViewProvider extends AbstractServiceProvider
{
    protected $provides = ['Chinstrap\Core\Contracts\Views\Renderer'];

    public function register(): void
    {
        // Register items
        $this->getContainer()
             ->add('Chinstrap\Core\Contracts\Views\Renderer', function () {
                 $container = $this->getContainer();
                 $twig = $container->get('Twig\Environment');
                 return new TwigRenderer($twig);
             });
    }
}
