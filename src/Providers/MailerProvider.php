<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Laminas\Mail\Transport\InMemory;
use Laminas\Mail\Transport\TransportInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

final class MailerProvider extends AbstractServiceProvider
{
    protected $provides = [TransportInterface::class];

    public function register(): void
    {
        $this->getContainer()
            ->add(TransportInterface::class, function () {
                return new InMemory();
            });
    }
}
