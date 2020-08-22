<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Console;

use Chinstrap\Core\Tests\TestCase;
use Chinstrap\Core\Tests\Traits\SetsPrivateProperties;
use Chinstrap\Core\Console\Runner;
use Chinstrap\Core\Kernel\Application;
use Mockery as m;

final class RunnerTest extends TestCase
{
    use SetsPrivateProperties;

    public function testExecute()
    {
        $console = m::mock('Symfony\Component\Console\Application');
        $console->shouldReceive('add')->times(5);
        $console->shouldReceive('run')->once();
        $container = m::mock('League\Container\Container');
        $container->shouldReceive('get')
            ->with('Symfony\Component\Console\Application')
            ->once()
            ->andReturn($console);
        $mockCommand = m::mock('Symfony\Component\Console\Command\Command');
        $container->shouldReceive('get')
            ->with('Chinstrap\Core\Console\Commands\FlushCache')
            ->once()
            ->andReturn($mockCommand);
        $container->shouldReceive('get')
            ->with('Chinstrap\Core\Console\Commands\Shell')
            ->once()
            ->andReturn($mockCommand);
        $container->shouldReceive('get')
            ->with('Chinstrap\Core\Console\Commands\Server')
            ->once()
            ->andReturn($mockCommand);
        $container->shouldReceive('get')
            ->with('Chinstrap\Core\Console\Commands\GenerateIndex')
            ->once()
            ->andReturn($mockCommand);
        $container->shouldReceive('get')
            ->with('Chinstrap\Core\Console\Commands\GenerateSitemap')
            ->once()
            ->andReturn($mockCommand);
        $mockApp = m::mock(new Application());
        $mockApp->shouldReceive('getContainer')
            ->once()
            ->andReturn($container);
        $runner = new Runner();
        $this->setPrivateProperty($runner, 'app', $mockApp);
        $runner();
    }

    public function testCatchError()
    {
        $this->expectOutputRegex('/^Unable to run/');
        $mockApp = m::mock(new Application());
        $mockApp->shouldReceive('getContainer')
            ->once()
            ->andThrow('Exception');
        $runner = new Runner();
        $this->setPrivateProperty($runner, 'app', $mockApp);
        $runner();
    }
}
