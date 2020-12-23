<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Events;

use Chinstrap\Core\Events\BaseEvent;
use Chinstrap\Core\Tests\TestCase;
use Mockery as m;

final class BaseEventTest extends TestCase
{
    public function testGetName(): void
    {
        $event = m::mock(BaseEvent::class)->makePartial();
        $this->assertEquals(BaseEvent::class, $event->getName());
    }
}
