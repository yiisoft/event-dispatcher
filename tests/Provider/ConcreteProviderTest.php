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

class ConcreteProviderTest extends TestCase
{
    /**
     * @var ConcreteProvider
     */
    private ConcreteProvider $concreteProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->concreteProvider = new ConcreteProvider();
    }

    public function testListenerForEventIsReturned(): void
    {
        $listener = fn () => null;

        $this->concreteProvider->attach(Event::class, $listener);

        $listeners = $this->concreteProvider->getListenersForEvent(new Event());

        $this->assertContains($listener, $listeners);
    }

    public function testDetachListenersForEventAreDetached(): void
    {
        $this->concreteProvider->attach(Event::class, fn () => null);
        $this->concreteProvider->detach(Event::class);

        $listeners = $this->concreteProvider->getListenersForEvent(new Event());

        $this->assertCount(0, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned(): void
    {
        $this->concreteProvider->attach(ParentInterface::class, function (ParentInterface $parentInterface) {
            $parentInterface->register('parent interface');
        });
        $this->concreteProvider->attach(ParentClass::class, function (ParentClass $parentClass) {
            $parentClass->register('parent class');
        });
        $this->concreteProvider->attach(ClassInterface::class, function (ClassInterface $classInterface) {
            $classInterface->register('class interface');
        });
        $this->concreteProvider->attach(ClassItself::class, function (ClassItself $classItself) {
            $classItself->register('class itself');
        });

        $event = new ClassItself();
        foreach ($this->concreteProvider->getListenersForEvent($event) as $listener) {
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
}
