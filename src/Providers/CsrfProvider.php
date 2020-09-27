<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Laminas\Session\Container;
use League\Container\ServiceProvider\AbstractServiceProvider;
use PublishingKit\Csrf\StoredTokenReader;
use PublishingKit\Csrf\StoredTokenValidator;

final class CsrfProvider extends AbstractServiceProvider
{
    protected $provides = [
                           TokenStorage::class,
                           StoredTokenReader::class,
                           StoredTokenValidator::class,
                          ];

    public function register(): void
    {
        // Register items
        $container = $this->getContainer();
        $container->add(TokenStorage::class, function () use ($container) {
            return new LaminasSessionTokenStorage($container->get(Container::class));
        });
        $container->add(StoredTokenReader::class, function () use ($container) {
            $storage = $container->get(TokenStorage::class);
            return new StoredTokenReader($storage);
        });
        $container->add(StoredTokenValidator::class, function () use ($container) {
            $storage = $container->get(TokenStorage::class);
            return new StoredTokenValidator($storage);
        });
    }
}
