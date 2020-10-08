<?php
declare(strict_types=1);

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

    public function flush(): void
    {
        foreach ($this->events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
