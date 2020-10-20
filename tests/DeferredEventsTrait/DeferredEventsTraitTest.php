<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\DeferredEventsTrait;

use PHPUnit\Framework\TestCase;

final class DeferredEventsTraitTest extends TestCase
{
    public function testBase(): void
    {
        $entity = new Entity();
        $entity->changeBody('test');
        $entity->recordEvent(new EntityPrepared());

        $events = $entity->releaseEvents();

        $this->assertCount(3, $events);
        $this->assertInstanceOf(EntityCreated::class, $events[0]);
        $this->assertInstanceOf(EntityModified::class, $events[1]);
        $this->assertInstanceOf(EntityPrepared::class, $events[2]);
        $this->assertEmpty($entity->releaseEvents());
    }
}
