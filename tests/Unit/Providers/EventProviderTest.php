<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;

final class EventProviderTest extends TestCase
{
    public function testCreateEventEmitter(): void
    {
        $emitter = $this->container->get(EventManagerInterface::class);
        $this->assertInstanceOf(EventManagerInterface::class, $emitter);
        $this->assertInstanceOf(EventManager::class, $emitter);
    }
}
