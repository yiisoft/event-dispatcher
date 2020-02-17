<?php

namespace Yiisoft\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Provider\ConcreteProvider;
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

        $provider->attach(Event::class, $listener);

        $listeners = $provider->getListenersForEvent(new Event());

        $listeners = \iterator_to_array($listeners, false);
        $this->assertCount(1, $listeners);
        $this->assertContains($listener, $listeners);
    }

    public function testDetachListenersForEventAreDetached(): void
    {
        $provider = $this->createConcreteProvider();

        $provider->attach(Event::class, fn () => null);
        $provider->detach(Event::class);

        $listeners = $provider->getListenersForEvent(new Event());

        $this->assertCount(0, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned(): void
    {
        $provider = $this->createConcreteProvider();

        $provider->attach(ParentInterface::class, function (ParentInterface $parentInterface) {
            $parentInterface->register('parent interface');
        });
        $provider->attach(ParentClass::class, function (ParentClass $parentClass) {
            $parentClass->register('parent class');
        });
        $provider->attach(ClassInterface::class, function (ClassInterface $classInterface) {
            $classInterface->register('class interface');
        });
        $provider->attach(ClassItself::class, function (ClassItself $classItself) {
            $classItself->register('class itself');
        });

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

    private function createConcreteProvider(): ConcreteProvider
    {
        return new ConcreteProvider();
    }
}
