<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class ConsoleProviderTest extends TestCase
{
    public function testCreateConsole(): void
    {
        $console = $this->container->get('Symfony\Component\Console\Application');
        $this->assertInstanceOf('Symfony\Component\Console\Application', $console);
    }
}
