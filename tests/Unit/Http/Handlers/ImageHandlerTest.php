<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Handlers;

use Chinstrap\Core\Http\Handlers\ImageHandler;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class ImageHandlerTest extends TestCase
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
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $request->shouldReceive('getQueryParams')
                ->once()
                ->andReturn([]);
        $imgResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $glide = m::mock('League\Glide\Server');
        $glide->shouldReceive('getImageResponse')
              ->once()
              ->with('foo', [])
              ->andReturn($imgResponse);
        $handler = new ImageHandler($glide);
        $response = $handler($request, ['name' => 'foo']);
        $this->assertSame($imgResponse, $response);
    }
}
