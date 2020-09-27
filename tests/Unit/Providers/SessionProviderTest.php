<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class SessionProviderTest extends TestCase
{
    public function testCreateSession(): void
    {
        $session = $this->container->get('Laminas\Session\Container');
        $this->assertInstanceOf('Laminas\Session\Container', $session);
        $this->assertInstanceOf('Laminas\Session\AbstractContainer', $session);
    }
}
