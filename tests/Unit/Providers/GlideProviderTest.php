<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Tests\TestCase;

final class GlideProviderTest extends TestCase
{
    public function testCreateFlysystem(): void
    {
        $fs = $this->container->get('League\Glide\Server');
        $this->assertInstanceOf('League\Glide\Server', $fs);
    }
}
