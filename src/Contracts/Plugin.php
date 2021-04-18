<?php

declare(strict_types=1);

namespace Chinstrap\Core\Contracts;

interface Plugin
{
    public function register(): void;
}
