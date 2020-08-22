<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class FlysystemProviderTest extends TestCase
{
    public function testCreateFlysystem(): void
    {
        $fs = $this->container->get('League\Flysystem\MountManager');
        $this->assertInstanceOf('League\Flysystem\MountManager', $fs);
    }
}
