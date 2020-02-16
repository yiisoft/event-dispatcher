<?php

namespace Yiisoft\EventDispatcher\Tests\Provider;

use InvalidArgumentException;
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

class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private Provider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new Provider();
    }

    public function testAttachCallableArray()
    {
        $this->provider->attach([WithStaticMethod::class, 'handle']);

        $listeners = $this->provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableFunction()
    {
        $this->provider->attach('Yiisoft\EventDispatcher\Tests\Provider\handle');

        $listeners = $this->provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachClosure()
    {
        $this->provider->attach(function (Event $event) {
            // do nothing
        });

        $listeners = $this->provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableObject()
    {
        $this->provider->attach([new NonStatic(), 'handle']);

        $listeners = $this->provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testInvokable()
    {
        $this->provider->attach(new Invokable());

        $listeners = $this->provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned()
    {
        $this->provider->attach(function (ParentInterface $parentInterface) {
            $parentInterface->register('parent interface');
        });
        $this->provider->attach(function (ParentClass $parentClass) {
            $parentClass->register('parent class');
        });
        $this->provider->attach(function (ClassInterface $classInterface) {
            $classInterface->register('class interface');
        });
        $this->provider->attach(function (ClassItself $classItself) {
            $classItself->register('class itself');
        });

        $event = new ClassItself();
        foreach ($this->provider->getListenersForEvent($event) as $listener) {
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

    public function testDetachListenersForEventAreDetached(): void
    {
        $this->provider->attach(fn (Event $event) => null);
        $this->provider->detach(Event::class);

        $listeners = $this->provider->getListenersForEvent(new Event());

        $this->assertCount(0, $listeners);
    }

    public function testListenerWithNoParameterThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Listeners must declare an object type they can accept.');

        $this->provider->attach(fn () => null);
    }
}

function handle(Event $event)
{
    // do nothing
}
