<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * DeferredProvider is a listener provider that allows you to perform deferred
 * events
 */
final class DeferredProvider implements ListenerProviderInterface
{
    private EventDispatcherInterface $dispatcher;
    private bool                     $deferEvents = false;
    private array                    $events = [];

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * returns a relevant listener or defer event
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($this->deferEvents) {
            yield fn ($event) => $this->addEvent($event);
        } else {
            yield fn ($event) => $this->dispatchEvent($event);
        }
    }

    /**
     * Enable deferred event mode
     */
    public function deferEvents(): void
    {
        $this->deferEvents = true;
    }

    /**
     * Deletes all deferred events
     */
    public function clearEvents(): void
    {
        $this->events = [];
    }

    /**
     * Dispatch all deferred events
     *
     * @return array dispatch events
     */
    public function dispatchEvents(): array
    {
        $events = $this->events;
        $this->clearEvents();
        $this->deferEvents = false;
        $dispatchedEvents = [];

        foreach ($events as $event) {
            $dispatchedEvents[] = $this->dispatchEvent($event);
        }

        return $dispatchedEvents;
    }

    private function addEvent(object $event): void
    {
        $this->events[] = $event;
    }

    private function dispatchEvent(object $event): object
    {
        return $this->dispatcher->dispatch($event);
    }
}
