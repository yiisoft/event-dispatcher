<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

/**
 * Provider of listeners from attributes for target object class name.
 */
final class AttributeListenersProvider
{
    /**
     * Get listeners from attributes defined in the target object class.
     *
     * @psalm-param class-string $targetClass
     */
    public static function getListenersFromAttributes(string $targetClass): ListenerCollection
    {
        $reflection = new ReflectionClass($targetClass);
        $attributes = $reflection->getAttributes(AttributeHandlerProvider::class, ReflectionAttribute::IS_INSTANCEOF);

        $listener = new ListenerCollection();

        foreach ($attributes as $attribute) {
            /** @var AttributeHandlerProvider $handlerProvider */
            $handlerProvider = $attribute->newInstance();

            foreach ($handlerProvider->getEventHandlers() as $event => $handler) {
                $listener = $listener->add($handler, $event);
            }
        }

        $properties = $reflection->getProperties(
            ReflectionProperty::IS_PRIVATE
            | ReflectionProperty::IS_PROTECTED
            | ReflectionProperty::IS_PUBLIC
        );

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(AttributeHandlerProvider::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var AttributeHandlerProvider $handlerProvider */
                $handlerProvider = $attribute->newInstance();
                $handlerProvider->setPropertyNames([$property->getName()]);

                foreach ($handlerProvider->getEventHandlers() as $event => $handler) {
                    $listener = $listener->add($handler, $event);
                }
            }
        }

        return $listener;
    }
}
