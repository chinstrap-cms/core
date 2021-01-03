<?php

declare(strict_types=1);

namespace Chinstrap\Core\Contracts\Sources;

use Chinstrap\Core\Contracts\Objects\Document;
use PublishingKit\Utilities\Contracts\Collectable;

interface Source
{
    public function all(): Collectable;

    public function find(string $name): ?Document;
}
