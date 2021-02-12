<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;
use function get_class;

/**
 * Provider is a listener provider that registers event listeners for interfaces used in callable type-hints
 * and gives out a list of handlers by event interface provided for further use with Dispatcher.
 *
 * ```php
 * $listeners = (new ListenerCollection())->add(function (AfterDocumentProcessed $event) {
 *    $document = $event->getDocument();
 *    // do something with document
 * });
 *
 * $provider = new Yiisoft\EventDispatcher\Provider\Provider($listeners);
 * ```
 */
final class Provider implements ListenerProviderInterface
{
    private ListenerCollection $listeners;

    public function __construct(ListenerCollection $listeners)
    {
        $this->listeners = $listeners;
    }

    public function getListenersForEvent(object $event): iterable
    {
        yield from $this->listeners->getForEvents(get_class($event));
        yield from $this->listeners->getForEvents(...array_values(class_parents($event)));
        yield from $this->listeners->getForEvents(...array_values(class_implements($event)));
    }
}
