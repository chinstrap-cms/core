<?php

declare(strict_types=1);

namespace Chinstrap\Tests\Unit\Core\Providers;

use Chinstrap\Tests\TestCase;

final class ViewProviderTest extends TestCase
{
    public function testCreateTwig(): void
    {
        $renderer = $this->container->get('Chinstrap\Core\Contracts\Views\Renderer');
        $this->assertInstanceOf('Chinstrap\Core\Contracts\Views\Renderer', $renderer);
        $this->assertInstanceOf('Chinstrap\Core\Views\TwigRenderer', $renderer);
    }
}
