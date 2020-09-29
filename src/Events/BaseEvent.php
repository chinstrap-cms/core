<?php

declare(strict_types=1);

namespace Chinstrap\Core\Events;

use Laminas\EventManager\Event;
use Laminas\EventManager\EventInterface;

abstract class BaseEvent extends Event implements EventInterface
{
    public function getName(): string
    {
        return self::class;
    }
}
