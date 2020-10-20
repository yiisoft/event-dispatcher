<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher;

trait DeferredEventsTrait
{
    private array $recordedEvents = [];

    public function recordEvent(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return object[]
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }
}
