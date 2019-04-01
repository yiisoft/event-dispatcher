<?php
namespace Yii\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

class ByInterface implements ListenerProviderInterface
{
    private $listeners = [];

    public function getListenersForEvent(object $event): iterable
    {
        // TODO: implement wildcards?

        $className = get_class($event);
        if (isset($this->listeners[$className])) {
            return $this->listeners[$className];
        }

        foreach (class_parents($event) as $parent) {
            if (isset($this->listeners[$parent])) {
                return $this->listeners[$parent];
            }
        }

        foreach (class_implements($event) as $interface) {
            if (isset($this->listeners[$interface])) {
                return $this->listeners[$interface];
            }
        }

        return [];
    }

    public function attach(string $interface, callable $listener): void
    {
        $this->listeners[$interface][] = $listener;
    }

    public function detach(string $interface): void
    {
        unset($this->listeners[$interface]);
    }
}
