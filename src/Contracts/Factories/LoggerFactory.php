<?php

declare(strict_types=1);

namespace Chinstrap\Core\Contracts\Factories;

use Psr\Log\LoggerInterface;
use PublishingKit\Config\Config;

interface LoggerFactory
{
    public function make(Config $config): LoggerInterface;
}
