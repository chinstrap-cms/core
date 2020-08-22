<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class YamlProviderTest extends TestCase
{
    public function testCreateYaml(): void
    {
        $renderer = $this->container->get('Mni\FrontYAML\Parser');
        $this->assertInstanceOf('Mni\FrontYAML\Parser', $renderer);
    }
}
