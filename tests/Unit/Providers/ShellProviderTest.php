<?php

declare(strict_types=1);

namespace Chinstrap\Tests\Unit\Core\Providers;

use Chinstrap\Tests\TestCase;

final class ShellProviderTest extends TestCase
{
    public function testCreateShell(): void
    {
        $shell = $this->container->get('Psy\Shell');
        $this->assertInstanceOf('Psy\Shell', $shell);
    }
}
