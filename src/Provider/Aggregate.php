<?php

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Aggregate is a listener provider that allows combining
 * multiple listener providers.
 */
final class Aggregate implements ListenerProviderInterface
{
    /**
     * @var ListenerProviderInterface[]
     */
    private array $providers = [];

    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->providers as $provider) {
            yield from $provider->getListenersForEvent($event);
        }
    }

    /**
     * Adds provider as a source for event listeners
     *
     * @param ListenerProviderInterface $provider
     */
    public function attach(ListenerProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }
}
