<?php

declare(strict_types=1);

namespace Chinstrap\Core\Console\Commands;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FlushCache extends Command
{
    protected CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        parent::__construct();
        $this->cache = $cache;
    }

    protected function configure(): void
    {
        $this->setName('cache:flush')
                ->setDescription('Flushes the cache')
                ->setHelp('This command will flush the cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cache->purge();
        return 1;
    }
}
