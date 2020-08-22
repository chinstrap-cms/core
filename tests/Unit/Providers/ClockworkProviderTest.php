<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class ClockworkProviderTest extends TestCase
{
    public function testCreateContainer(): void
    {
        $clockwork = $this->container->get('Clockwork\Support\Vanilla\Clockwork');
        $this->assertInstanceOf('Clockwork\Support\Vanilla\Clockwork', $clockwork);
    }
}
