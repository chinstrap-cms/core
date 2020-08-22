<?php

declare(strict_types=1);

namespace Chinstrap\Tests\Unit\Core\Providers;

use Chinstrap\Tests\TestCase;

final class RouterProviderTest extends TestCase
{
    public function testCreateFlysystem(): void
    {
        $router = $this->container->get('League\Route\Router');
        $this->assertInstanceOf('League\Route\Router', $router);
    }
}
