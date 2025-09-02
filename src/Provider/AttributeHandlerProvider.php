<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use Closure;

/**
 * Abstract class for event handlers provider based on class attributes.
 *
 * Each handler can be associated with one or more property names.
 *
 * To use this class, extend it and implement the `getEventHandlers()` method to return an array
 * mapping event class names to their corresponding handler closures.
 *
 * For example:
 *
 * ```php
 * #[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
 * class SetValueOnUpdate extends AttributeHandlerProvider
 * {
 *     public function __construct(
 *         private mixed $value = null,
 *         string ...$propertyNames,
 *     ) {
 *         parent::__construct(...$propertyNames);
 *     }
 *
 *     public function getEventHandlers(): array
 *     {
 *         return [
 *             BeforeUpdate::class => $this->beforeUpdate(...),
 *         ];
 *     }
 *
 *     private function beforeUpdate(BeforeUpdate $event): void
 *     {
 *         $target = $event->getTarget(); // Assuming the event has a method to get the target object
 *         $value = is_callable($this->value) ? ($this->value)($event) : $this->value;
 *
 *         foreach ($this->getPropertyNames() as $propertyName) {
               $target->$propertyName = $value;
 *         }
 *     }
 * }
 * ```
 */
abstract class AttributeHandlerProvider
{
    /**
     * @var string[] List of property names the handler should be applied to.
     */
    private array $propertyNames;

    /**
     * Returns array with event class names as keys and their handlers as values `[event_class => handler_closure, ...]`
     *
     * @psalm-return array<class-string, Closure>
     */
    abstract public function getEventHandlers(): array;

    /**
     * @param string ...$propertyNames Names of properties the handler should be applied to.
     */
    public function __construct(
        string ...$propertyNames,
    ) {
        $this->propertyNames = $propertyNames;
    }

    /**
     * Returns the list of property names the handler should be applied to.
     *
     * @return string[]
     */
    public function getPropertyNames(): array
    {
        return $this->propertyNames;
    }

    /**
     * Sets the list of property names the handler should be applied to.
     *
     * @param string[] $propertyNames
     */
    public function setPropertyNames(array $propertyNames): void
    {
        $this->propertyNames = $propertyNames;
    }
}
