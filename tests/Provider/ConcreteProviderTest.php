<?php

namespace Yiisoft\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\EventDispatcher\Tests\Event\ClassInterface;
use Yiisoft\EventDispatcher\Tests\Event\ClassItself;
use Yiisoft\EventDispatcher\Tests\Event\Event;
use Yiisoft\EventDispatcher\Tests\Event\ParentClass;
use Yiisoft\EventDispatcher\Tests\Event\ParentInterface;

use function array_slice;

final class ConcreteProviderTest extends TestCase
{
    public function testListenerForEventIsReturned(): void
    {
        $provider = $this->createConcreteProvider();

        $listener = fn () => null;

        $provider->attach($listener, Event::class);

        $listeners = $provider->getListenersForEvent(new Event());

        $listeners = \iterator_to_array($listeners, false);
        $this->assertCount(1, $listeners);
        $this->assertContains($listener, $listeners);
    }

    public function testDetachListenersForEventAreDetached(): void
    {
        $provider = $this->createConcreteProvider();

        $provider->attach(fn () => null, Event::class);
        $provider->detach(Event::class);

        $listeners = $provider->getListenersForEvent(new Event());

        $this->assertCount(0, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned(): void
    {
        $provider = $this->createConcreteProvider();

        $provider->attach(function (ParentInterface $parentInterface) {
            $parentInterface->register('parent interface');
        }, ParentInterface::class);
        $provider->attach(function (ParentClass $parentClass) {
            $parentClass->register('parent class');
        }, ParentClass::class);
        $provider->attach(function (ClassInterface $classInterface) {
            $classInterface->register('class interface');
        }, ClassInterface::class);
        $provider->attach(function (ClassItself $classItself) {
            $classItself->register('class itself');
        }, ClassItself::class);

        $event = new ClassItself();
        foreach ($provider->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        $classHierarchyHandlers = array_slice($event->registered(), 0, 2);
        $interfaceHandlers = array_slice($event->registered(), 2);

        $this->assertEquals(
            [
                'class itself',
                'parent class',
            ],
            $classHierarchyHandlers
        );

        $this->assertContains('class interface', $interfaceHandlers);
        $this->assertContains('parent interface', $interfaceHandlers);
    }

    private function createConcreteProvider(): Provider
    {
        return new Provider();
    }
}
