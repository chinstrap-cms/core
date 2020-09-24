<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Kernel;

use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Chinstrap\Core\Kernel\Kernel;
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

    public function testErrorHandler()
    {
        $handler = m::mock('Chinstrap\Core\Contracts\Exceptions\Handler');
        $router = m::mock('League\Route\Router');
        $router->shouldReceive('get')
            ->andReturn($router);
        $router->shouldReceive('middleware')
            ->andReturn($router);
        $router->shouldReceive('post')
            ->andReturn($router);
        $container = m::mock('League\Container\Container');
        $container->shouldReceive('delegate')->once();
        $container->shouldReceive('addServiceProvider');
        $container->shouldReceive('share')->times(2);
        $container->shouldReceive('get')->with('League\Route\Router')
            ->once()
            ->andReturn($router);
        $container->shouldReceive('get')->with('Chinstrap\Core\Contracts\Exceptions\Handler')
            ->once()
            ->andReturn($handler);
        $container->shouldReceive('get')->with('PublishingKit\Config\Config')
            ->once()
            ->andReturn(new Config([]));
        $app = new Application($container);
        $app->bootstrap();
    }

    public function testPluginNotFound()
    {
        $this->expectException('Chinstrap\Core\Exceptions\Plugins\PluginNotFound');
        $handler = m::mock('Chinstrap\Core\Contracts\Exceptions\Handler');
        $container = m::mock('League\Container\Container');
        $container->shouldReceive('delegate')->once();
        $container->shouldReceive('addServiceProvider');
        $container->shouldReceive('share')->times(2);
        $container->shouldReceive('get')->with('My\Nonexistent\Plugin')
            ->once()
            ->andReturn(null);
        $container->shouldReceive('get')->with('Chinstrap\Core\Contracts\Exceptions\Handler')
            ->once()
            ->andReturn($handler);
        $config = new Config([
                              'plugins' => ['My\Nonexistent\Plugin'],
                             ]);
        $container->shouldReceive('get')->with('PublishingKit\Config\Config')
            ->once()
            ->andReturn($config);
        $app = new Kernel($container);
        $app->bootstrap();
    }

    public function testPluginNotValid()
    {
        $this->expectException('Chinstrap\Core\Exceptions\Plugins\PluginNotValid');
        $handler = m::mock('Chinstrap\Core\Contracts\Exceptions\Handler');
        $container = m::mock('League\Container\Container');
        $container->shouldReceive('delegate')->once();
        $container->shouldReceive('addServiceProvider');
        $container->shouldReceive('share')->times(2);
        $container->shouldReceive('get')->with('stdClass')
            ->once()
            ->andReturn(new \stdClass());
        $container->shouldReceive('get')->with('Chinstrap\Core\Contracts\Exceptions\Handler')
            ->once()
            ->andReturn($handler);
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
