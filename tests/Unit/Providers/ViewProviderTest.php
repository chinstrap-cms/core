<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class ViewProviderTest extends TestCase
{
    public function testCreateTwig(): void
    {
        $renderer = $this->container->get('Chinstrap\Core\Contracts\Views\Renderer');
        $this->assertInstanceOf('Chinstrap\Core\Contracts\Views\Renderer', $renderer);
        $this->assertInstanceOf('Chinstrap\Core\Views\TwigRenderer', $renderer);
    }
}
