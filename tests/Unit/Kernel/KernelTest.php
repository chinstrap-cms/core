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

    public function testPluginNotFound()
    {
        $this->expectException('Chinstrap\Core\Exceptions\Plugins\PluginNotFound');
        $container = m::mock('Psr\Container\ContainerInterface');
        $container->shouldReceive('get')->with('My\Nonexistent\Plugin')
            ->once()
            ->andReturn(null);
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
        $container = m::mock('Psr\Container\ContainerInterface');
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
