<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;
use Laminas\Mail\Transport\InMemory;
use Laminas\Mail\Transport\TransportInterface;

final class MailerProviderTest extends TestCase
{
    public function testCreateSession(): void
    {
        $mailer = $this->container->get(TransportInterface::class);
        $this->assertInstanceOf(TransportInterface::class, $mailer);
        $this->assertInstanceOf(InMemory::class, $mailer);
    }
}
