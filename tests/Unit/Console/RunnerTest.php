<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Console;

use Chinstrap\Core\Tests\TestCase;
use Chinstrap\Core\Tests\Traits\SetsPrivateProperties;
use Chinstrap\Core\Console\Runner;
use Chinstrap\Core\Kernel\Kernel;
use Mockery as m;

final class RunnerTest extends TestCase
{
    use SetsPrivateProperties;

    public function testExecute()
    {
        $console = m::mock('Symfony\Component\Console\Kernel');
        $console->shouldReceive('add')->times(5);
        $console->shouldReceive('run')->once();
        $container = m::mock('Psr\Container\ContainerInterface');
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
        $mockApp = m::mock(new Kernel());
        $mockApp->shouldReceive('getContainer')
            ->once()
            ->andReturn($container);
        $runner = new Runner();
        $this->setPrivateProperty($runner, 'kernel', $mockApp);
        $runner();
    }

    public function testCatchError()
    {
        $this->expectOutputRegex('/^Unable to run/');
        $mockApp = m::mock(new Kernel());
        $mockApp->shouldReceive('getContainer')
            ->once()
            ->andThrow('Exception');
        $runner = new Runner();
        $this->setPrivateProperty($runner, 'kernel', $mockApp);
        $runner();
    }
}
