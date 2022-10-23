<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * CompositeProvider is a listener provider that allows combining multiple listener providers.
 */
final class CompositeProvider implements ListenerProviderInterface
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
     * Adds provider as a source for event listeners.
     */
    public function attach(ListenerProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }
}
