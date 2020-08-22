<?php

declare(strict_types=1);

namespace Chinstrap\Core\Contracts\Generators;

/**
 * Interface for sitemap generators
 */
interface Sitemap
{
    public function __invoke(): string;
}
