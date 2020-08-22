<?php

declare(strict_types=1);

namespace Chinstrap\Tests\Unit\Core\Providers;

use Chinstrap\Tests\TestCase;

final class HandlerProviderTest extends TestCase
{
    public function testCreateHandler(): void
    {
        $handler = $this->container->get('Chinstrap\Core\Contracts\Exceptions\Handler');
        $this->assertInstanceOf('Chinstrap\Core\Contracts\Exceptions\Handler', $handler);
        $this->assertInstanceOf('Chinstrap\Core\Exceptions\LogHandler', $handler);
    }
}
