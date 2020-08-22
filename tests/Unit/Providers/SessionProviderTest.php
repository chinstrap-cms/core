<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class SessionProviderTest extends TestCase
{
    public function testCreateSession(): void
    {
        $session = $this->container->get('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\SessionInterface', $session);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Session', $session);
    }
}
