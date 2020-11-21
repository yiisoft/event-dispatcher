<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

/**
 * Listener collection stores listeners and is used to configure provider.
 *
 * @see Provider
 */
final class ListenerCollection
{
    /**
     * @var callable[][]
     */
    private array $listeners = [];

    /**
     * @param string ...$eventClassNames
     *
     * @return iterable<callable>
     */
    public function getForEvents(string ...$eventClassNames): iterable
    {
        foreach ($eventClassNames as $eventClassName) {
            if (isset($this->listeners[$eventClassName])) {
                yield from $this->listeners[$eventClassName];
            }
        }
    }

    /**
     * Attaches listener to corresponding event based on the type-hint used for the event argument.
     *
     * Method signature should be the following:
     *
     * ```
     *  function (MyEvent $event): void
     * ```
     *
     * Any callable could be used be it a closure, invokable object or array referencing a class or object.
     *
     * @param callable $listener
     * @param string $eventClassName
     *
     * @return self
     */
    public function add(callable $listener, string $eventClassName = ''): self
    {
        $new = clone $this;

        if ($eventClassName === '') {
            $eventClassName = $this->getParameterType($listener);
        }

        $new->listeners[$eventClassName][] = $listener;
        return $new;
    }

    /**
     * Derives the interface type of the first argument of a callable.
     *
     * @suppress PhanUndeclaredMethod
     *
     * @param callable $callable The callable for which we want the parameter type.
     *
     * @return string The interface the parameter is type hinted on.
     */
    private function getParameterType(callable $callable): string
    {
        // This try-catch is only here to keep OCD linters happy about uncaught reflection exceptions.
        try {
            $closure = new \ReflectionFunction(\Closure::fromCallable($callable));
            $params = $closure->getParameters();

            $reflectedType = isset($params[0]) ? $params[0]->getType() : null;
            if ($reflectedType === null) {
                throw new \InvalidArgumentException('Listeners must declare an object type they can accept.');
            }
            $type = $reflectedType->getName();
        } catch (\ReflectionException $e) {
            throw new \RuntimeException('Type error registering listener.', 0, $e);
        }

        return $type;
    }
}
