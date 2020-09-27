<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use Laminas\Session\Container;
use League\Container\ServiceProvider\AbstractServiceProvider;

class SessionProvider extends AbstractServiceProvider
{
    protected $provides = [Container::class];

    public function register(): void
    {
        // Register items
        $this->getContainer()
             ->share(Container::class, function () {
                 return new Container();
             });
    }
}
