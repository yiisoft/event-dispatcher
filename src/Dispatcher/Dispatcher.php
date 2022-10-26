<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Dispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Dispatcher executes listeners attached to event passed.
 *
 * @see https://www.php-fig.org/psr/psr-14/
 */
final class Dispatcher implements EventDispatcherInterface
{
    public function __construct(private ListenerProviderInterface $listenerProvider)
    {
    }

    public function dispatch(object $event): object
    {
        /** @var callable $listener */
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }

            $spoofableEvent = $event;
            $listener($spoofableEvent);
        }

        return $event;
    }
}
