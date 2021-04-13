<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Factories;

use Chinstrap\Core\Factories\FlysystemFactory;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class FlysystemFactoryTest extends TestCase
{
    public function testLocal(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'local',
                              'path' => 'content/',
                             ]);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('League\Flysystem\Adapter\Local', $cache->getAdapter());
    }

    public function testLocalByDefault(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make(['path' => 'content/']);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('League\Flysystem\Adapter\Local', $cache->getAdapter());
    }

    public function testLocalMisconfigured(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([]);
    }

    public function testMemory(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make(['driver' => 'memory']);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('League\Flysystem\Memory\MemoryAdapter', $cache->getAdapter());
    }

    public function testDropbox(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'dropbox',
                              'token' => 'foo',
                             ]);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('Spatie\FlysystemDropbox\DropboxAdapter', $cache->getAdapter());
    }

    public function testDropboxMisconfigured(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make(['driver' => 'dropbox']);
    }

    public function testAzure(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'azure',
                              'container' => 'foo',
                              'name' => 'bar',
                              'key' => 'baz',
                             ]);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter', $cache->getAdapter());
    }

    public function testAzureMisconfiguredContainer(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'azure',
                              'name' => 'bar',
                              'key' => 'baz',
                             ]);
    }

    public function testAzureMisconfiguredName(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'azure',
                              'container' => 'foo',
                              'key' => 'baz',
                             ]);
    }

    public function testAzureMisconfiguredKey(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'azure',
                              'container' => 'foo',
                              'name' => 'bar',
                             ]);
    }

    public function testS3(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 's3',
                              'bucket' => 'foo',
                              'key' => 'bar',
                              'secret' => 'baz',
                              'region' => 'foo',
                              'version' => 'latest',
                             ]);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('League\Flysystem\AwsS3v3\AwsS3Adapter', $cache->getAdapter());
    }

    public function testS3MisconfiguredBucket(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 's3',
                              'secret' => 'baz',
                              'region' => 'foo',
                              'version' => 'latest',
                             ]);
    }

    public function testS3MisconfiguredKey(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 's3',
                              'bucket' => 'foo',
                              'secret' => 'baz',
                              'region' => 'foo',
                              'version' => 'latest',
                             ]);
    }

    public function testS3MisconfiguredSecret(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 's3',
                              'bucket' => 'foo',
                              'key' => 'bar',
                              'region' => 'foo',
                              'version' => 'latest',
                             ]);
    }

    public function testS3MisconfiguredRegion(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 's3',
                              'bucket' => 'foo',
                              'key' => 'bar',
                              'secret' => 'baz',
                              'version' => 'latest',
                             ]);
    }

    public function testS3MisconfiguredVersion(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 's3',
                              'bucket' => 'foo',
                              'key' => 'bar',
                              'secret' => 'baz',
                              'region' => 'foo',
                             ]);
    }

    public function testSFTP(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'sftp',
                              'host' => 'foo.com',
                              'username' => 'bob',
                              'password' => 'password',
                              'root' => 'foo',
                             ]);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('League\Flysystem\Sftp\SftpAdapter', $cache->getAdapter());
    }

    public function testSFTPMisconfiguredHost(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'sftp',
                              'username' => 'bob',
                              'password' => 'password',
                              'root' => 'foo',
                             ]);
    }

    public function testSFTPMisconfiguredUsername(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'sftp',
                              'host' => 'foo.com',
                              'password' => 'password',
                              'root' => 'foo',
                             ]);
    }

    public function testSFTPMisconfiguredPassword(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'sftp',
                              'host' => 'foo.com',
                              'username' => 'bob',
                              'root' => 'foo',
                             ]);
    }

    public function testFTP(): void
    {
        $pool = m::mock('Stash\Pool');
        $pool->shouldReceive('getItem')->once()->andReturn($pool);
        $pool->shouldReceive('get')->once()->andReturn(false);
        $pool->shouldReceive('isMiss')->once()->andReturn(true);
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'ftp',
                              'host' => 'foo.com',
                              'username' => 'bob',
                              'password' => 'password',
                              'root' => 'foo',
                             ]);
        $this->assertInstanceOf('League\Flysystem\Filesystem', $fs);
        $cache = $fs->getAdapter();
        $this->assertInstanceOf('League\Flysystem\Cached\CachedAdapter', $cache);
        $this->assertInstanceOf('League\Flysystem\Adapter\Ftp', $cache->getAdapter());
    }

    public function testFTPMisconfiguredHost(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'ftp',
                              'username' => 'bob',
                              'password' => 'password',
                              'root' => 'foo',
                             ]);
    }

    public function testFTPMisconfiguredUsername(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'ftp',
                              'host' => 'foo.com',
                              'password' => 'password',
                              'root' => 'foo',
                             ]);
    }

    public function testFTPMisconfiguredPassword(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException');
        $pool = m::mock('Stash\Pool');
        $factory = new FlysystemFactory($pool);
        $fs = $factory->make([
                              'driver' => 'ftp',
                              'host' => 'foo.com',
                              'username' => 'bob',
                              'root' => 'foo',
                             ]);
    }
}
