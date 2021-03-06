<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Exceptions;

use Chinstrap\Core\Exceptions\LogHandler;
use Chinstrap\Core\Tests\TestCase;
use Exception;
use Mockery as m;

final class LogHandlerTest extends TestCase
{
    public function testHandleException(): void
    {
        $logger = m::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('error');
        $inspector = m::mock('Whoops\Exception\Inspector');
        $run = m::mock('Whoops\RunInterface');
        $handler = new LogHandler($logger);
        $e = new Exception('Test message');
        $handler($e, $inspector, $run);
    }
}
