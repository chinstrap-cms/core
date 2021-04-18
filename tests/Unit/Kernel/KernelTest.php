<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Kernel;

use Chinstrap\Core\Kernel\Kernel;
use Chinstrap\Core\Tests\TestCase;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Mockery as m;
use PublishingKit\Config\Config;

final class KernelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        putenv('APP_ENV=production');
    }

    public function tearDown(): void
    {
        putenv('APP_ENV=testing');
        error_reporting(E_ALL);
        parent::tearDown();
    }

    public function testPluginNotFound(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Plugins\PluginNotFound');
        $emitter = m::mock('Laminas\EventManager\EventManagerInterface');
        $container = m::mock('Psr\Container\ContainerInterface');
        $container->shouldReceive('get')
                  ->with('Laminas\EventManager\EventManagerInterface')
                  ->once()
                  ->andReturn($emitter);
        $container->shouldReceive('get')->with('My\Nonexistent\Plugin')
            ->once()
            ->andThrow(ServiceNotFoundException::class);
        $config = new Config([
                              'plugins' => ['My\Nonexistent\Plugin'],
                             ]);
        $container->shouldReceive('get')->with('PublishingKit\Config\Config')
            ->once()
            ->andReturn($config);
        $app = new Kernel($container);
        $app->bootstrap();
    }

    public function testPluginNotValid(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Plugins\PluginNotValid');
        $emitter = m::mock('Laminas\EventManager\EventManagerInterface');
        $container = m::mock('Psr\Container\ContainerInterface');
        $container->shouldReceive('get')
                  ->with('Laminas\EventManager\EventManagerInterface')
                  ->once()
                  ->andReturn($emitter);
        $container->shouldReceive('get')->with('stdClass')
            ->once()
            ->andReturn(new \stdClass());
        $config = new Config([
                              'plugins' => ['stdClass'],
                             ]);
        $container->shouldReceive('get')->with('PublishingKit\Config\Config')
            ->once()
            ->andReturn($config);
        $app = new Kernel($container);
        $app->bootstrap();
    }
}
