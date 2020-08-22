<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

final class FormsProvider extends AbstractServiceProvider
{
    protected $provides = ['Chinstrap\Core\Contracts\Factories\FormFactory'];

    public function register(): void
    {
        // Register items
        $this->getContainer()->add(
            'Chinstrap\Core\Contracts\Factories\FormFactory',
            'Chinstrap\Core\Factories\Forms\LaminasFormFactory'
        );
    }
}
