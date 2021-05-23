<?php

declare(strict_types=1);

namespace Chinstrap\Core\Factories;

use Aws\S3\S3Client;
use Chinstrap\Core\Exceptions\Configuration\RootDirNotDefined;
use Chinstrap\Core\Exceptions\Factories\BadFlysystemConfigurationException;
use League\Flysystem\Adapter\Ftp as FTPAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Stash as StashStore;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use League\Flysystem\Sftp\SftpAdapter;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use PublishingKit\Config\Config;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Stash\Pool;

final class FlysystemFactory
{
    private Pool $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    public function make(Config $config): Filesystem
    {
        switch ($config->get('driver')) {
            case 'memory':
                $adapter = $this->createMemoryAdapter();
                break;
            case 'dropbox':
                $adapter = $this->createDropboxAdapter($config);
                break;
            case 'azure':
                $adapter = $this->createAzureAdapter($config);
                break;
            case 's3':
                $adapter = $this->createS3Adapter($config);
                break;
            case 'sftp':
                $adapter = $this->createSftpAdapter($config);
                break;
            case 'ftp':
                $adapter = $this->createFtpAdapter($config);
                break;
            default:
                $adapter = $this->createLocalAdapter($config);
                break;
        }
        $cache = new StashStore($this->pool, 'storageKey', 300);
        return new Filesystem(
            new CachedAdapter(
                $adapter,
                $cache
            )
        );
    }

    private function createMemoryAdapter(): MemoryAdapter
    {
        return new MemoryAdapter();
    }

    private function createLocalAdapter(Config $config): Local
    {
        if (!defined('ROOT_DIR')) {
            throw new RootDirNotDefined('Root directory not defined');
        }
        if (!$config->has('path') || !is_string($config->get('path'))) {
            throw new BadFlysystemConfigurationException('Path not set for local driver');
        }
        return new Local(ROOT_DIR . '/' . $config->get('path'));
    }

    private function createDropboxAdapter(Config $config): DropboxAdapter
    {
        if (!$config->has('token') || !is_string($config->get('token'))) {
            throw new BadFlysystemConfigurationException('Token not set for Dropbox driver');
        }
        $client = new Client($config->get('token'));
        return new DropboxAdapter($client);
    }

    private function createAzureAdapter(Config $config): AzureBlobStorageAdapter
    {
        if (!$config->has('container') || !is_string($config->get('container'))) {
            throw new BadFlysystemConfigurationException('Container not set for Azure driver');
        }
        if (!$config->has('name') || !is_string($config->get('name'))) {
            throw new BadFlysystemConfigurationException('Account name not set for Azure driver');
        }
        if (!$config->has('key') || !is_string($config->get('key'))) {
            throw new BadFlysystemConfigurationException('Account key not set for Azure driver');
        }
        $endpoint = sprintf(
            'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s',
            $config->get('name'),
            $config->get('key')
        );
        $client = BlobRestProxy::createBlobService($endpoint);
        return new AzureBlobStorageAdapter($client, $config['container']);
    }

    private function createS3Adapter(Config $config): AwsS3Adapter
    {
        if (!$config->has('bucket')) {
            throw new BadFlysystemConfigurationException('Bucket not set for S3 driver');
        }
        if (!$config->has('key')) {
            throw new BadFlysystemConfigurationException('Key not set for S3 driver');
        }
        if (!$config->has('secret')) {
            throw new BadFlysystemConfigurationException('Secret not set for S3 driver');
        }
        if (!$config->has('region')) {
            throw new BadFlysystemConfigurationException('Region not set for S3 driver');
        }
        if (!$config->has('version')) {
            throw new BadFlysystemConfigurationException('Version not set for S3 driver');
        }
        $client = new S3Client([
                                'credentials' => [
                                                  'key' => $config->get('key'),
                                                  'secret' => $config->get('secret'),
                                                 ],
                                'region' => $config->get('region'),
                                'version' => $config->get('version'),
                               ]);
        return new AwsS3Adapter($client, $config->get('bucket'));
    }

    private function createSftpAdapter(Config $config): SftpAdapter
    {
        if (!$config->has('host')) {
            throw new BadFlysystemConfigurationException('Host not set for SFTP driver');
        }
        if (!$config->has('username')) {
            throw new BadFlysystemConfigurationException('Username not set for SFTP driver');
        }
        if (!$config->has('password') && !$config->has('privatekey')) {
            throw new BadFlysystemConfigurationException('Neither password nor private key set for SFTP driver');
        }
        return new SftpAdapter([
                                'host' => $config->get('host'),
                                'port' => $config->get('port') ?? 22,
                                'username' => $config->get('username'),
                                'password' => $config->get('password'),
                                'privateKey' => $config->get('privatekey') ?? null,
                                'root' => $config->get('root') ?? null,
                                'timeout' => $config->get('timeout') ?? 10,
                               ]);
    }

    private function createFtpAdapter(Config $config): FTPAdapter
    {
        if (!$config->has('host')) {
            throw new BadFlysystemConfigurationException('Host not set for FTP driver');
        }
        if (!$config->has('username')) {
            throw new BadFlysystemConfigurationException('Username not set for FTP driver');
        }
        if (!$config->has('password') && !$config->has('privatekey')) {
            throw new BadFlysystemConfigurationException('Neither password nor private key set for FTP driver');
        }
        return new FTPAdapter([
                                'host' => $config->get('host'),
                                'port' => $config->get('port') ?? 22,
                                'username' => $config->get('username'),
                                'password' => $config->get('password'),
                                'privateKey' => $config->get('privatekey') ?? null,
                                'root' => $config->get('root') ?? null,
                                'timeout' => $config->get('timeout') ?? 10,
                              ]);
    }
}
