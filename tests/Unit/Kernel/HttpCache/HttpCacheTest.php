<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Kernel\HttpCache;

use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Chinstrap\Core\Kernel\HttpCache\HttpCache;

final class HttpCacheTest extends TestCase
{
    public function testHandle()
    {
        $request = m::spy('Psr\Http\Message\ServerRequestInterface');
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $psr6 = m::mock('Psr\Cache\CacheItemPoolInterface');
        $kernel = m::mock('Chinstrap\Core\Contracts\Kernel\KernelInterface');
        $kernel->shouldReceive('handle')
            ->with($request)
            ->once()
            ->andReturn($response);
        $cache = new HttpCache($kernel, $psr6);
        $result = $cache->handle($request);
        $this->assertSame($result, $response);
    }
}
