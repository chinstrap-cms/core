<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Chinstrap\Core\Views\TwigRenderer;

final class ViewProvider extends AbstractServiceProvider
{
    protected $provides = ['Chinstrap\Core\Contracts\Views\Renderer'];

    public function register(): void
    {
        // Register items
        $this->getContainer()
                ->add('Chinstrap\Core\Contracts\Views\Renderer', function () {
                    $twig = $this->getContainer()->get('Twig\Environment');
                    return new TwigRenderer($twig);
                });
    }
}
