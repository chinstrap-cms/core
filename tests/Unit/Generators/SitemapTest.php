<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Generators;

use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Chinstrap\Core\Objects\MarkdownDocument;
use Chinstrap\Core\Generators\XmlStringSitemap;
use PublishingKit\Utilities\Collections\Collection;
use PublishingKit\Config\Config;

final class SitemapTest extends TestCase
{
    public function testExecute()
    {
        $expectedResponse = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://mysite.com/foo</loc>
  </url>
</urlset>
EOF;
        $doc = new MarkdownDocument();
        $doc->setContent('This is my content');
        $doc->setField('title', 'My Page');
        $doc->setField('layout', 'custom.html');
        $doc->setPath('foo.md');
        $docs = Collection::make([$doc]);

        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('all')->once()->andReturn($docs);
        $config = m::mock('PublishingKit\Config\Config');
        $config->shouldReceive('get')
            ->with('base_url')
            ->once()
            ->andReturn('https://mysite.com');
        $generator = new XmlStringSitemap($config, $source);
        $response = $generator();
        $this->assertEquals(trim($expectedResponse), trim($response));
    }
}
