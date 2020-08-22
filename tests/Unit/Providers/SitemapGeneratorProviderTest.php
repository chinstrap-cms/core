<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Providers;

use Chinstrap\Core\Tests\TestCase;

final class SitemapGeneratorProviderTest extends TestCase
{
    public function testCreateSouce(): void
    {
        $generator = $this->container->get('Chinstrap\Core\Contracts\Generators\Sitemap');
        $this->assertInstanceOf('Chinstrap\Core\Contracts\Generators\Sitemap', $generator);
        $this->assertInstanceOf('Chinstrap\Core\Generators\XmlStringSitemap', $generator);
    }
}
