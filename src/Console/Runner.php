<?php

declare(strict_types=1);

namespace Chinstrap\Core\Console;

use Chinstrap\Core\Kernel\Kernel;
use Exception;
use Psr\Container\ContainerInterface;

final class Runner
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke()
    {
        try {
            /** @var Symfony\Component\Console\Application $console **/
            $console = $this->container->get(\Symfony\Component\Console\Application::class);
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\FlushCache::class));
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\Shell::class));
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\Server::class));
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\GenerateIndex::class));
            $console->add($this->container->get(\Chinstrap\Core\Console\Commands\GenerateSitemap::class));
            $console->run();
        } catch (Exception $err) {
            $this->returnError($err);
        }
    }

    private function returnError(Exception $err): void
    {
        $msg = "Unable to run - " . $err->getMessage();
        $msg .= "\n" . $err->__toString();
        $msg .= "\n";
        echo $msg;
    }
}
