<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class TwigProviderTest extends TestCase
{
    public function testCreateTwig(): void
    {
        $twig = $this->container->get('Twig\Environment');
        $this->assertInstanceOf('Twig\Environment', $twig);
    }
}
