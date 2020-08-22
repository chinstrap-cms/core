<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class ContainerProviderTest extends TestCase
{
    public function testCreateContainer(): void
    {
        $container = $this->container->get('Psr\Container\ContainerInterface');
        $this->assertInstanceOf('Psr\Container\ContainerInterface', $container);
        $this->assertInstanceOf('League\Container\Container', $container);
    }
}
