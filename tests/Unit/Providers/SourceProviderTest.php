<?php

declare(strict_types=1);

namespace Chinstrap\Tests\Unit\Core\Providers;

use Chinstrap\Tests\TestCase;

final class SourceProviderTest extends TestCase
{
    public function testCreateSouce(): void
    {
        $source = $this->container->get('Chinstrap\Core\Contracts\Sources\Source');
        $this->assertInstanceOf('Chinstrap\Core\Contracts\Sources\Source', $source);
        $this->assertInstanceOf('Chinstrap\Core\Sources\MarkdownFiles', $source);
    }
}
