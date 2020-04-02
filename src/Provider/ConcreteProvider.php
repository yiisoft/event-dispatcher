<?php

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

use function array_values;
use function class_implements;
use function class_parents;
use function get_class;

/**
 * ConcreteProvider is a listener provider that registers event listeners for interface names specified explicitly
 * and gives out a list of handlers for further use with Dispatcher.
 *
 * ```php
 * $provider = new Yiisoft\EventDispatcher\Provider\ConcreteProvider();
 * $provider->attach(SomeEvent::class, function () {
 *    // handle it
 * });
 * ```
 */
final class ConcreteProvider implements ListenerProviderInterface
{
    /**
     * @var callable[]
     */
    private array $listeners = [];

    /**
     * @param object $event
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        yield from $this->listenersFor(get_class($event));
        yield from $this->listenersFor(...array_values(class_parents($event)));
        yield from $this->listenersFor(...array_values(class_implements($event)));
    }

    /**
     * Attach an event handler for the given event name
     *
     * @param string $eventClassName
     * @param callable $listener
     */
    public function attach(callable $listener, string $eventClassName): void
    {
        $this->listeners[$eventClassName][] = $listener;
    }

    /**
     * Detach all event handlers registered for an interface
     *
     * @param string $eventClassName
     */
    public function detach(string $eventClassName): void
    {
        unset($this->listeners[$eventClassName]);
    }

    /**
     * @param string ...$eventClassNames
     * @return iterable<callable>
     */
    private function listenersFor(string ...$eventClassNames): iterable
    {
        foreach ($eventClassNames as $eventClassName) {
            if (isset($this->listeners[$eventClassName])) {
                yield from $this->listeners[$eventClassName];
            }
        }
    }
}
