<?php

namespace Chinstrap\Core\Contracts\Kernel;

use Psr\Container\ContainerInterface;

interface KernelInterface
{
    public function getContainer(): ContainerInterface;

    public function bootstrap(): void;
}
