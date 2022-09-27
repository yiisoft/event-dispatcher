<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

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
    public function __construct(private ListenerCollection $listeners)
    {
    }

    public function getListenersForEvent(object $event): iterable
    {
        yield from $this->listeners->getForEvents($event::class);
        /** @psalm-suppress MixedArgument */
        yield from $this->listeners->getForEvents(...array_values(class_parents($event)));
        /** @psalm-suppress MixedArgument */
        yield from $this->listeners->getForEvents(...array_values(class_implements($event)));
    }
}
