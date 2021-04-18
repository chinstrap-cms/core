<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Handlers;

use Chinstrap\Core\Http\Handlers\ClockworkHandler;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class ClockworkHandlerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if (!defined('E_STRICT')) {
            return;
        }

        error_reporting('E_ALL | E_STRICT');
    }

    public function tearDown(): void
    {
        error_reporting(E_ALL);
        parent::tearDown();
    }

    public function testGetResponse(): void
    {
        $clockwork = m::mock('Clockwork\Support\Vanilla\Clockwork')->makePartial();
        $clockwork->shouldReceive('getMetadata')
            ->with('foo')
            ->once()
            ->andReturn(['bar' => 'baz']);
        $handler = new ClockworkHandler($clockwork);
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = $handler($request, ['request' => 'foo']);
        $this->assertEquals(json_encode(['bar' => 'baz']), $response->getBody()->getContents());
    }
}
