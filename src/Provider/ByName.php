<?php
namespace Yii\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;
use Yii\EventDispatcher\NamedEvent;

class ByName implements ListenerProviderInterface
{
    /**
     * @var array
     */
    private $listeners = [];

    public function getListenersForEvent(object $event): iterable
    {
        if (!$event instanceof NamedEvent) {
            return [];
        }

        // TODO: implement wildcards?

        $name = $event->getName();
        return $this->listeners[$name];
    }

    public function attach(string $name, callable $listener): void
    {
        $this->listeners[$name][] = $listener;
    }

    public function detach(string $name): void
    {
        unset($this->listeners[$name]);
    }
}
