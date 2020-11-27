<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Support;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

final class ListenerConfigurationChecker
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function check(array $configuration): void
    {
        foreach ($configuration as $eventName => $listeners) {
            if (!is_string($eventName)) {
                throw new InvalidEventConfigurationFormatException(
                    'Incorrect event listener format. Format with event name must be used.'
                );
            }

            if (!is_array($listeners)) {
                $type = gettype($listeners);

                throw new InvalidEventConfigurationFormatException(
                    "Event listeners for $eventName must be an array, $type given."
                );
            }

            foreach ($listeners as $listener) {
                try {
                    if (!$this->isCallable($listener)) {
                        $type = gettype($listener);

                        throw new InvalidListenerConfigurationException(
                            "Listener must be a callable, $type given."
                        );
                    }
                } catch (ContainerExceptionInterface $exception) {
                    throw new InvalidListenerConfigurationException(
                        "Could not instantiate event listener or listener class has invalid configuration.",
                        0,
                        $exception
                    );
                }
            }
        }
    }

    private function isCallable($definition): bool
    {
        if (is_callable($definition)) {
            return true;
        }

        if (
            is_array($definition)
            && array_keys($definition) === [0, 1]
            && is_string($definition[0])
            && $this->container->has($definition[0])
        ) {
            $object = $this->container->get($definition[0]);

            return method_exists($object, $definition[1]);
        }

        return false;
    }
}
