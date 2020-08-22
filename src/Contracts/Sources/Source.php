<?php

declare(strict_types=1);

namespace Chinstrap\Core\Contracts\Sources;

use PublishingKit\Utilities\Contracts\Collectable;
use Chinstrap\Core\Contracts\Objects\Document;

interface Source
{
    public function all(): Collectable;

    public function find(string $name): ?Document;
}
