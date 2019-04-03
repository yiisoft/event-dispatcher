<?php

namespace Yii\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Yii\EventDispatcher\Provider\Provider;
use Yii\EventDispatcher\Tests\Event\ClassItself;
use Yii\EventDispatcher\Tests\Event\Event;
use Yii\EventDispatcher\Tests\Event\ParentClass;
use Yii\EventDispatcher\Tests\Event\ParentInterface;
use Yii\EventDispatcher\Tests\Event\ClassInterface;
use Yii\EventDispatcher\Tests\Listener\Invokable;
use Yii\EventDispatcher\Tests\Listener\NonStatic;
use Yii\EventDispatcher\Tests\Listener\WithStaticMethod;

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
        $provider->attach('Yii\EventDispatcher\Tests\Provider\handle');

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
