<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class ShellProviderTest extends TestCase
{
    public function testCreateShell(): void
    {
        $shell = $this->container->get('Psy\Shell');
        $this->assertInstanceOf('Psy\Shell', $shell);
    }
}
