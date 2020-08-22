<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Chinstrap\Core\Exceptions\LogHandler;

final class HandlerProvider extends AbstractServiceProvider
{
    protected $provides = ['Chinstrap\Core\Contracts\Exceptions\Handler'];

    public function register(): void
    {
        // Register items
        $this->getContainer()
            ->add('Chinstrap\Core\Contracts\Exceptions\Handler', function () {
                return new LogHandler($this->getContainer()->get('Psr\Log\LoggerInterface'));
            });
    }
}
