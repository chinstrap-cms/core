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
            $console = $this->container->get('Symfony\Component\Console\Application');
            $console->add($this->container->get('Chinstrap\Core\Console\Commands\FlushCache'));
            $console->add($this->container->get('Chinstrap\Core\Console\Commands\Shell'));
            $console->add($this->container->get('Chinstrap\Core\Console\Commands\Server'));
            $console->add($this->container->get('Chinstrap\Core\Console\Commands\GenerateIndex'));
            $console->add($this->container->get('Chinstrap\Core\Console\Commands\GenerateSitemap'));
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
