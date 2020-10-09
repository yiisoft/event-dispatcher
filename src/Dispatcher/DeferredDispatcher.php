<?php

namespace Yiisoft\EventDispatcher\Dispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;

final class DeferredDispatcher implements EventDispatcherInterface
{
    private EventDispatcherInterface $dispatcher;
    private array $events = [];

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(object $event)
    {
        $this->events[] = $event;
        return $event;
    }

    public function flush(): array
    {
        $dispatchedEvents = [];
        foreach ($this->events as $event) {
            $dispatchedEvents[] = $this->dispatcher->dispatch($event);
        }

        $this->events = [];
        return $dispatchedEvents;
    }
}
