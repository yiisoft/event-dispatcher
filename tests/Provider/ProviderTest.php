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

class ProviderTest extends TestCase
{
    public function testAttachCallableArray()
    {
        $provider = new Provider();
        $provider->attach([WithStaticMethod::class, 'handle']);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableFunction()
    {
        $provider = new Provider();
        $provider->attach('Yiisoft\EventDispatcher\Tests\Provider\handle');

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachClosure()
    {
        $provider = new Provider();
        $provider->attach(function (Event $event) {
            // do nothing
        });

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableObject()
    {
        $provider = new Provider();
        $provider->attach([new NonStatic(), 'handle']);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testInvokable()
    {
        $provider = new Provider();
        $provider->attach(new Invokable());

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned()
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
}

function handle(Event $event)
{
    // do nothing
}
