<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Dispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Delegates dispatching an event to one or more dispatchers.
 */
final class CompositeDispatcher implements EventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface[]
     */
    private array $dispatchers = [];

    public function dispatch(object $event)
    {
        foreach ($this->dispatchers as $dispatcher) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }
            $event = $dispatcher->dispatch($event);
        }

        return $event;
    }

    public function attach(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatchers[] = $dispatcher;
    }
}
