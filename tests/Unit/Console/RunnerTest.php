<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Console;

use Chinstrap\Core\Console\Runner;
use Chinstrap\Core\Tests\TestCase;
use Chinstrap\Core\Tests\Traits\SetsPrivateProperties;
use Mockery as m;

final class RunnerTest extends TestCase
{
    use SetsPrivateProperties;

    public function testExecute(): void
    {
        $console = m::mock('Symfony\Component\Console\Kernel');
        $console->shouldReceive('add')->times(5);
        $console->shouldReceive('run')->once();
        $container = m::mock('Psr\Container\ContainerInterface');
        $container->shouldReceive('get')
            ->with('Symfony\Component\Console\Application')
            ->once()
            ->andReturn($console);
        $mockShell = m::mock('Psy\Shell');
        $mockCommand = m::mock('Symfony\Component\Console\Command\Command');
        $container->shouldReceive('get')
            ->with('Chinstrap\Core\Console\Commands\FlushCache')
            ->once()
            ->andReturn($mockCommand);
        $container->shouldReceive('get')
            ->with('Psy\Shell')
            ->once()
            ->andReturn($mockShell);
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
        $runner = new Runner($container);
        $runner();
    }

    public function testCatchError(): void
    {
        $this->expectOutputRegex('/^Unable to run/');
        $container = m::mock('Psr\Container\ContainerInterface');
        $runner = new Runner($container);
        $runner();
    }
}
