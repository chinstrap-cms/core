<?php

declare(strict_types=1);

namespace Chinstrap\Core\Console;

use Psr\Container\ContainerInterface;
use Psy\Shell as Psysh;
use Symfony\Component\Console\Application;

final class Runner
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function returnError(\Throwable $err): void
    {
        $msg = "Unable to run - " . $err->getMessage();
        $msg .= "\n" . $err->__toString();
        $msg .= "\n";
        echo $msg;
    }

    public function __invoke(): void
    {
        try {
            $console = $this->container->get(Application::class);
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\FlushCache::class));
            $console->add(
                new \Chinstrap\Core\Console\Commands\Shell(
                    $this->container,
                    $this->container->get(Psysh::class)
                )
            );
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\Server::class));
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\GenerateIndex::class));
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\GenerateSitemap::class));
            $console->run();
        } catch (\Throwable $err) {
            $this->returnError($err);
        }
    }
}
