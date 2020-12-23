<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Exceptions;

use Chinstrap\Core\Exceptions\ErrorHandler;
use Chinstrap\Core\Tests\TestCase;
use Exception;
use Mockery as m;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class ErrorHandlerTest extends TestCase
{
    public function testProcessError(): void
    {
        $stream = m::mock(StreamInterface::class);
        $stream->shouldReceive('write')
               ->with("An error occurred: Something went wrong")
               ->once();
        $response = m::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')
                 ->once()
                 ->andReturn($stream);
        $factory = m::mock(ResponseFactoryInterface::class);
        $factory->shouldReceive('createResponse')
                ->with(500)
                ->andReturn($response);
        $exception = new Exception("Something went wrong");
        $handler = new ErrorHandler($factory);
        $result = $handler($exception);
        $this->assertEquals($response, $result);
    }
}
