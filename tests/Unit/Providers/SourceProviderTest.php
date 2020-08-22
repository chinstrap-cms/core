<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class SourceProviderTest extends TestCase
{
    public function testCreateSouce(): void
    {
        $source = $this->container->get('Chinstrap\Core\Contracts\Sources\Source');
        $this->assertInstanceOf('Chinstrap\Core\Contracts\Sources\Source', $source);
        $this->assertInstanceOf('Chinstrap\Core\Sources\MarkdownFiles', $source);
    }
}
