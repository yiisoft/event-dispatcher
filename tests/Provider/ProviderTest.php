<?php

namespace Yiisoft\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\EventDispatcher\Tests\Event\ClassItself;
use Yiisoft\EventDispatcher\Tests\Event\Event;
use Yiisoft\EventDispatcher\Tests\Event\ParentClass;
use Yiisoft\EventDispatcher\Tests\Event\ParentInterface;
use Yiisoft\EventDispatcher\Tests\Event\ClassInterface;
use Yiisoft\EventDispatcher\Tests\Listener\Invokable;
use Yiisoft\EventDispatcher\Tests\Listener\NonStatic;
use Yiisoft\EventDispatcher\Tests\Listener\WithStaticMethod;

final class ProviderTest extends TestCase
{
    public function testAttachCallableArray(): void
    {
        $provider = new Provider();
        $provider->attach([WithStaticMethod::class, 'handle']);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableFunction(): void
    {
        $provider = new Provider();
        $provider->attach('Yiisoft\EventDispatcher\Tests\Provider\handle');

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachClosure(): void
    {
        $provider = new Provider();
        $provider->attach(function (Event $event) {
            // do nothing
        });

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableObject(): void
    {
        $provider = new Provider();
        $provider->attach([new NonStatic(), 'handle']);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testInvokable(): void
    {
        $provider = new Provider();
        $provider->attach(new Invokable());

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned(): void
    {
        $provider = new Provider();

        $provider->attach(function (ParentInterface $parentInterface) {
            $parentInterface->register('parent interface');
        });
        $provider->attach(function (ParentClass $parentClass) {
            $parentClass->register('parent class');
        });
        $provider->attach(function (ClassInterface $classInterface) {
            $classInterface->register('class interface');
        });
        $provider->attach(function (ClassItself $classItself) {
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
                'parent class'
            ],
            $classHierarchyHandlers
        );

        $this->assertContains('class interface', $interfaceHandlers);
        $this->assertContains('parent interface', $interfaceHandlers);
    }

    public function testListenerWithNoParameterThrowsException(): void
    {
        $provider = new Provider();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Listeners must declare an object type they can accept.');

        $provider->attach(fn () => null);
    }

    public function testListenerForEventIsReturned(): void
    {
        $provider = new Provider();

        $listener = fn () => null;

        $provider->attach($listener, Event::class);

        $listeners = $provider->getListenersForEvent(new Event());

        $listeners = \iterator_to_array($listeners, false);
        $this->assertCount(1, $listeners);
        $this->assertContains($listener, $listeners);
    }

    public function testDetachListenersForEventAreDetached(): void
    {
        $provider = new Provider();

        $provider->attach(fn () => null, Event::class);
        $provider->detach(Event::class);

        $listeners = $provider->getListenersForEvent(new Event());

        $this->assertCount(0, $listeners);
    }
}

function handle(Event $event): void
{
    // do nothing
}
