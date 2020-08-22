<?php

declare(strict_types=1);

namespace Chinstrap\Core\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Flysystem\FilesystemInterface;
use Chinstrap\Core\Contracts\Generators\Sitemap;

final class GenerateSitemap extends Command
{
    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * @var FilesystemInterface
     */
    protected $manager;

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
