<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionUnionType;

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
     * Get listeners for event class names specified.
     *
     * @param string ...$eventClassNames Event class names.
     *
     * @return iterable<callable> Listeners.
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
     * @param string ...$eventClassNames
     *
     * @throws InvalidArgumentException If callable is invalid.
     */
    public function add(callable $listener, string ...$eventClassNames): self
    {
        $new = clone $this;

        if ($eventClassNames === []) {
            $eventClassNames = $this->getParameterType($listener);
        }

        foreach ($eventClassNames as $eventClassName) {
            $new->listeners[$eventClassName][] = $listener;
        }
        return $new;
    }

    /**
     * Derives the interface type of the first argument of a callable.
     *
     * @param callable $callable The callable for which we want the parameter type.
     *
     * @throws InvalidArgumentException If callable is invalid.
     *
     * @return string[] Interfaces the parameter is type hinted on.
     */
    private function getParameterType(callable $callable): array
    {
        $closure = new ReflectionFunction(Closure::fromCallable($callable));
        $params = $closure->getParameters();

        if (isset($params[0])) {
            $reflectedType = $params[0]->getType();
        } else {
            throw new InvalidArgumentException('Listeners must accept an event object.');
        }

        if ($reflectedType instanceof ReflectionNamedType) {
            return [$reflectedType->getName()];
        }

        /** @psalm-suppress UndefinedClass,TypeDoesNotContainType */
        if ($reflectedType instanceof ReflectionUnionType) {
            /** @var ReflectionNamedType[] */
            $types = $reflectedType->getTypes();
            return array_map(
                static fn (ReflectionNamedType $type) => $type->getName(),
                $types
            );
        }

        throw new InvalidArgumentException('Listeners must declare an object type they can accept.');
    }
}
