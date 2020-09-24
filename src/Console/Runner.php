<?php

declare(strict_types=1);

namespace Chinstrap\Core\Console;

use Chinstrap\Core\Kernel\Kernel;
use Dotenv\Dotenv;
use Exception;

final class Runner
{
    /**
     * @var Kernel
     */
    protected $kernel;

    public function __construct()
    {
        $this->kernel = new Kernel();
    }

    public function __invoke()
    {
        try {
            $this->kernel->bootstrap();
            $container = $this->kernel->getContainer();
            $console = $container->get('Symfony\Component\Console\Application');
            $console->add($container->get('Chinstrap\Core\Console\Commands\FlushCache'));
            $console->add($container->get('Chinstrap\Core\Console\Commands\Shell'));
            $console->add($container->get('Chinstrap\Core\Console\Commands\Server'));
            $console->add($container->get('Chinstrap\Core\Console\Commands\GenerateIndex'));
            $console->add($container->get('Chinstrap\Core\Console\Commands\GenerateSitemap'));
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
