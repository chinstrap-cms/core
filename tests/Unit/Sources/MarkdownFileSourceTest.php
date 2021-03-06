<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Sources;

use Chinstrap\Core\Sources\MarkdownFiles;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class MarkdownFileSourceTest extends TestCase
{
    public function testAll(): void
    {
        $manager = m::mock('League\Flysystem\MountManager');
        $manager->shouldReceive('listContents')
            ->with('content://', true)
            ->andReturn([
                         [
                          'type' => 'dir',
                          'path' => 'about',
                         ],
                         [
                          'type' => 'file',
                          'path' => 'foo.md',
                         ],
                         [
                          'type' => 'file',
                          'path' => 'bar.pdf',
                         ],
                        ]);
        $manager->shouldReceive('read')->with('content://foo.md')
            ->andReturn('foo');
        $manager->shouldReceive('getTimestamp')->with('content://foo.md')
            ->andReturn(1568840820);
        $document = m::mock('Mni\FrontYAML\Document');
        $document->shouldReceive('getContent')
            ->andReturn('My content');
        $document->shouldReceive('getYAML')
            ->andReturn(['title' => 'Foo']);
        $parser = m::mock('Mni\FrontYAML\Parser');
        $parser->shouldReceive('parse')->with('foo')
            ->andReturn($document);
        $source = new MarkdownFiles($manager, $parser);
        $response = $source->all();
        $this->assertCount(1, $response);
        $item = $response->toArray()[0];
        $this->assertInstanceOf('Chinstrap\Core\Objects\MarkdownDocument', $item);
        $this->assertEquals('My content', $item->content);
        $this->assertEquals('Foo', $item->title);
        $this->assertEquals('foo.md', $item->path);
    }

    public function testFind(): void
    {
        $manager = m::mock('League\Flysystem\MountManager');
        $manager->shouldReceive('has')->with('content://foo.md')
            ->once()
            ->andReturn(true);
        $manager->shouldReceive('read')->with('content://foo.md')
            ->once()
            ->andReturn('foo');
        $manager->shouldReceive('getTimestamp')->with('content://foo.md')
            ->once()
            ->andReturn(1568840820);
        $document = m::mock('Mni\FrontYAML\Document');
        $document->shouldReceive('getContent')->once()
            ->andReturn('My content');
        $document->shouldReceive('getYAML')->once()
            ->andReturn(['title' => 'Foo']);
        $parser = m::mock('Mni\FrontYAML\Parser');
        $parser->shouldReceive('parse')->with('foo')->once()
            ->andReturn($document);
        $source = new MarkdownFiles($manager, $parser);
        $document = $source->find('foo');
        $this->assertInstanceOf('Chinstrap\Core\Objects\MarkdownDocument', $document);
        $this->assertEquals('My content', $document->content);
        $this->assertEquals('Foo', $document->title);
        $this->assertEquals('foo.md', $document->path);
    }

    public function testFindEmpty(): void
    {
        $manager = m::mock('League\Flysystem\MountManager');
        $manager->shouldReceive('has')->with('content://foo.md')
            ->once()
            ->andReturn(true);
        $manager->shouldReceive('read')->with('content://foo.md')
            ->once()
            ->andReturn(null);
        $parser = m::mock('Mni\FrontYAML\Parser');
        $source = new MarkdownFiles($manager, $parser);
        $this->assertNull($source->find('foo'));
    }

    public function testFindNull(): void
    {
        $manager = m::mock('League\Flysystem\MountManager');
        $manager->shouldReceive('has')->with('content://foo.md')
            ->once()
            ->andReturn(null);
        $parser = m::mock('Mni\FrontYAML\Parser');
        $source = new MarkdownFiles($manager, $parser);
        $this->assertNull($source->find('foo'));
    }
}
