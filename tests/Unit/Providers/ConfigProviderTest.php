<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class ConfigProviderTest extends TestCase
{
    public function testCreateContainer(): void
    {
        $config = $this->container->get('PublishingKit\Config\Config');
        $this->assertInstanceOf('PublishingKit\Config\Config', $config);
    }
}
