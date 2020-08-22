<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class LoggerProviderTest extends TestCase
{
    public function testCreateLogger(): void
    {
        $logger = $this->container->get('Psr\Log\LoggerInterface');
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $logger);
        $this->assertInstanceOf('Monolog\Logger', $logger);
    }
}
