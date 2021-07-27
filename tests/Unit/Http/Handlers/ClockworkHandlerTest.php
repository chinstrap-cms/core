<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Handlers;

use Chinstrap\Core\Http\Handlers\ClockworkHandler;
use Chinstrap\Core\Tests\TestCase;
use Clockwork\Support\Vanilla\Clockwork;
use Mockery as m;

final class ClockworkHandlerTest extends TestCase
{
    public function testGetResponse(): void
    {
        $clockwork = $this->createStub(Clockwork::class);
        $clockwork->method('getMetadata')
            ->willReturn(['bar' => 'baz']);
        $handler = new ClockworkHandler($clockwork);
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $response = $handler($request, ['request' => 'foo']);
        $this->assertEquals(json_encode(['bar' => 'baz']), $response->getBody()->getContents());
    }
}
