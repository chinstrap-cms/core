<?php

declare(strict_types=1);

namespace Chinstrap\Tests\Unit\Core\Providers;

use Chinstrap\Tests\TestCase;

final class LoggerProviderTest extends TestCase
{
    public function testCreateLogger(): void
    {
        $logger = $this->container->get('Psr\Log\LoggerInterface');
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $logger);
        $this->assertInstanceOf('Monolog\Logger', $logger);
    }
}
