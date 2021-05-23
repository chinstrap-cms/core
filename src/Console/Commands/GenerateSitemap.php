<?php

declare(strict_types=1);

namespace Chinstrap\Core\Console\Commands;

use Chinstrap\Core\Contracts\Generators\Sitemap;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateSitemap extends Command
{
    protected Sitemap $sitemap;

    protected FilesystemInterface $manager;

    public function __construct(Sitemap $sitemap, FilesystemInterface $manager)
    {
        parent::__construct();
        $this->sitemap = $sitemap;
        $this->manager = $manager;
    }

    protected function configure(): void
    {
        $this->setName('sitemap:generate')
                ->setDescription('Generates the sitemap')
                ->setHelp('This command will generate the sitemap');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->manager->put('assets://sitemap.xml', $this->sitemap->__invoke());
        return 1;
    }
}
