<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Chinstrap\Core\Events\RegisterViewHelpers;
use Chinstrap\Core\Views\TwigRenderer;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Event\EmitterInterface;

final class ViewProvider extends AbstractServiceProvider
{
    protected $provides = ['Chinstrap\Core\Contracts\Views\Renderer'];

    public function register(): void
    {
        // Register items
        $this->getContainer()
                ->add('Chinstrap\Core\Contracts\Views\Renderer', function () {
                    $container = $this->getContainer();
                    $emitter = $container->get(EmitterInterface::class);
                    $event = $container->get(RegisterViewHelpers::class);
                    $emitter->emit($event);
                    $twig = $container->get('Twig\Environment');
                    return new TwigRenderer($twig);
                });
    }
}
