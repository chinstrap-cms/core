<?php

declare(strict_types=1);

namespace Chinstrap\Core\Events;

use Laminas\EventManager\Event;

abstract class BaseEvent extends Event
{
    public function getName(): string
    {
        return self::class;
    }
}
