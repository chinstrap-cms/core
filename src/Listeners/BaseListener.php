<?php

declare(strict_types=1);

namespace Chinstrap\Core\Listeners;

use Laminas\EventManager\EventInterface;

abstract class BaseListener extends AbstractListener
{
    abstract public function __invoke(EventInterface $event): void;
}
