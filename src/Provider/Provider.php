<?php

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Provider is a listener provider that registers event listeners for interfaces used in callable type-hints
 * and gives out a list of handlers by event interface provided for further use with Dispatcher.
 *
 * ```php
 * $provider = new Yiisoft\EventDispatcher\Provider\Provider();
 *
 * // adding some listeners
 * $provider->attach(function (AfterDocumentProcessed $event) {
 *    $document = $event->getDocument();
 *    // do something with document
 * });
 * ```
 */
final class Provider implements ListenerProviderInterface
{
    private ConcreteProvider $concreteProvider;

    public function __construct()
    {
        $this->concreteProvider = new ConcreteProvider();
    }

    /**
     * @param object $event
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        yield from $this->concreteProvider->getListenersForEvent($event);
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
     */
    public function attach(callable $listener): void
    {
        $eventName = $this->getParameterType($listener);

        $this->concreteProvider->attach($eventName, $listener);
    }

    /**
     * Detach all event handlers registered for an interface
     *
     * @param string $interface
     */
    public function detach(string $interface): void
    {
        $this->concreteProvider->detach($interface);
    }

    /**
     * Derives the interface type of the first argument of a callable.
     *
     * @param callable $callable The callable for which we want the parameter type.
     * @return string The interface the parameter is type hinted on.
     */
    private function getParameterType(callable $callable): string
    {
        // This try-catch is only here to keep OCD linters happy about uncaught reflection exceptions.
        try {
            switch (true) {
                // See note on isClassCallable() for why this must be the first case.
                case $this->isClassCallable($callable):
                    $reflect = new \ReflectionClass($callable[0]);
                    $params = $reflect->getMethod($callable[1])->getParameters();
                    break;
                case $this->isFunctionCallable($callable):
                case $this->isClosureCallable($callable):
                    $reflect = new \ReflectionFunction($callable);
                    $params = $reflect->getParameters();
                    break;
                case $this->isObjectCallable($callable):
                    $reflect = new \ReflectionObject($callable[0]);
                    $params = $reflect->getMethod($callable[1])->getParameters();
                    break;
                case $this->isInvokable($callable):
                    $params = (new \ReflectionMethod($callable, '__invoke'))->getParameters();
                    break;
                default:
                    throw new \InvalidArgumentException('Not a recognized type of callable');
            }

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

    /**
     * Determines if a callable represents a function.
     *
     * Or at least a reasonable approximation, since a function name may not be defined yet.
     *
     * @param callable $callable
     * @return True if the callable represents a function, false otherwise.
     */
    private function isFunctionCallable(callable $callable): bool
    {
        // We can't check for function_exists() because it may be included later by the time it matters.
        return is_string($callable);
    }

    /**
     * Determines if a callable represents a closure/anonymous function.
     *
     * @param callable $callable
     * @return True if the callable represents a closure object, false otherwise.
     */
    private function isClosureCallable(callable $callable): bool
    {
        return $callable instanceof \Closure;
    }

    /**
     * @param callable $callable
     * @return True if the callable represents an invokable object, false otherwise.
     */
    private function isInvokable(callable $callable): bool
    {
        return is_object($callable);
    }

    /**
     * Determines if a callable represents a method on an object.
     *
     * @param callable $callable
     * @return True if the callable represents a method object, false otherwise.
     */
    private function isObjectCallable(callable $callable): bool
    {
        return is_array($callable) && is_object($callable[0]);
    }

    /**
     * Determines if a callable represents a static class method.
     *
     * The parameter here is untyped so that this method may be called with an
     * array that represents a class name and a non-static method.  The routine
     * to determine the parameter type is identical to a static method, but such
     * an array is still not technically callable.  Omitting the parameter type here
     * allows us to use this method to handle both cases.
     *
     * Note that this method must therefore be the first in the switch statement
     * above, or else subsequent calls will break as the array is not going to satisfy
     * the callable type hint but it would pass `is_callable()`.  Because PHP.
     *
     * @param callable $callable
     * @return True if the callable represents a static method, false otherwise.
     */
    private function isClassCallable($callable): bool
    {
        return is_array($callable) && is_string($callable[0]) && class_exists($callable[0]);
    }
}
