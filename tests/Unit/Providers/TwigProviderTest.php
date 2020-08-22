<?php

declare(strict_types=1);

namespace Chinstrap\Tests\Unit\Core\Providers;

use Chinstrap\Tests\TestCase;

final class TwigProviderTest extends TestCase
{
    public function testCreateTwig(): void
    {
        $twig = $this->container->get('Twig\Environment');
        $this->assertInstanceOf('Twig\Environment', $twig);
    }
}
