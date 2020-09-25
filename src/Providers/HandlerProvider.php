<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Chinstrap\Core\Contracts\Exceptions\Handler;
use Chinstrap\Core\Exceptions\LogHandler;
use League\Container\ServiceProvider\AbstractServiceProvider;

final class HandlerProvider extends AbstractServiceProvider
{
    protected $provides = [Handler::class];

    public function register(): void
    {
        // Register items
        $this->getContainer()
            ->add(Handler::class, function () {
                return new LogHandler($this->getContainer()->get('Psr\Log\LoggerInterface'));
            });
    }
}
