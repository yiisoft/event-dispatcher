<?php

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

use function array_values;
use function class_implements;
use function class_parents;
use function get_class;

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
     * @param string $eventName
     * @param callable $listener
     */
    public function attach(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * Detach all event handlers registered for an interface
     *
     * @param string $interface
     */
    public function detach(string $interface): void
    {
        unset($this->listeners[$interface]);
    }

    /**
     * @param string ...$eventNames
     * @return iterable<callable>
     */
    private function listenersFor(string ...$eventNames): iterable
    {
        foreach ($eventNames as $name) {
            if (isset($this->listeners[$name])) {
                yield from $this->listeners[$name];
            }
        }
    }
}
