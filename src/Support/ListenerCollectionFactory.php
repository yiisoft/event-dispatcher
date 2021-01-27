<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Support;

use Psr\Container\ContainerInterface;
use ReflectionMethod;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\Injector\Injector;

final class ListenerCollectionFactory
{
    private Injector $injector;
    private ContainerInterface $container;

    public function __construct(Injector $injector, ContainerInterface $container)
    {
        $this->injector = $injector;
        $this->container = $container;
    }

    /**
     * @param array $eventListeners Event listener list in format ['eventName1' => [$listener1, $listener2, ...]]
     *
     * @return ListenerCollection
     */
    public function create(array $eventListeners): ListenerCollection
    {
        $listenerCollection = new ListenerCollection();

        foreach ($eventListeners as $eventName => $listeners) {
            if (!is_string($eventName) || !class_exists($eventName)) {
                throw new InvalidEventConfigurationFormatException(
                    'Incorrect event listener format. Format with event name must be used.'
                );
            }

            if (!is_iterable($listeners)) {
                $type = gettype($listeners);

                throw new InvalidEventConfigurationFormatException(
                    "Event listeners for $eventName must be an iterable, $type given."
                );
            }

            foreach ($listeners as $callable) {
                $listener = function (object $event) use ($callable) {
                    if (is_array($callable) && !is_object($callable[0])) {
                        $reflection = new ReflectionMethod($callable[0], $callable[1]);
                        if (!$reflection->isStatic()) {
                            $callable = [$this->container->get($callable[0]), $callable[1]];
                        }
                    }

                    return $this->injector->invoke($callable, [$event]);
                };
                $listenerCollection = $listenerCollection->add($listener, $eventName);
            }
        }

        return $listenerCollection;
    }
}
