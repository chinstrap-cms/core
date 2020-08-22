<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class RouterProviderTest extends TestCase
{
    public function testCreateFlysystem(): void
    {
        $router = $this->container->get('League\Route\Router');
        $this->assertInstanceOf('League\Route\Router', $router);
    }
}
