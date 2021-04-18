<?php

declare(strict_types=1);

namespace Chinstrap\Core\Contracts\Kernel;

interface KernelInterface
{
    public function bootstrap(): void;
}
