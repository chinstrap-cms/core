<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Sources\Decorators;

use Chinstrap\Core\Objects\MarkdownDocument;
use Chinstrap\Core\Sources\Decorators\Psr6CacheDecorator;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use PublishingKit\Utilities\Collections\Collection;

final class Psr6CacheDecoratorTest extends TestCase
{
    public function testAll(): void
    {
        $result = Collection::make([]);
        $cache = m::mock('Psr\Cache\CacheItemPoolInterface');
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('all')->once()->andReturn($result);
        $decorator = new Psr6CacheDecorator($cache, $source);
        $decorator->all();
    }

    public function testFindHit(): void
    {
        $result = new MarkdownDocument();
        $cache = m::mock('Psr\Cache\CacheItemPoolInterface');
        $cache->shouldReceive('getItem')->once()->andReturn($cache);
        $cache->shouldReceive('isHit')->once()->andReturn(true);
        $cache->shouldReceive('get')->once()->andReturn($result);
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $decorator = new Psr6CacheDecorator($cache, $source);
        $decorator->find('foo');
    }

    public function testFindMiss(): void
    {
        $result = new MarkdownDocument();
        $item = m::mock('Psr\Cache\CacheItemInterface');
        $cache = m::mock('Psr\Cache\CacheItemPoolInterface');
        $cache->shouldReceive('getItem')->once()->andReturn($item);
        $cache->shouldReceive('save')->once()->with($item);
        $item->shouldReceive('isHit')->once()->andReturn(false);
        $item->shouldReceive('set')->once()->with($result);
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('find')->with('foo')->once()->andReturn($result);
        $decorator = new Psr6CacheDecorator($cache, $source);
        $decorator->find('foo');
    }
}
