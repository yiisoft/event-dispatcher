<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\EventDispatcher\Tests\Event\ClassInterface;
use Yiisoft\EventDispatcher\Tests\Event\ClassItself;
use Yiisoft\EventDispatcher\Tests\Event\Event;
use Yiisoft\EventDispatcher\Tests\Event\ParentClass;
use Yiisoft\EventDispatcher\Tests\Event\ParentInterface;
use Yiisoft\EventDispatcher\Tests\Listener\Invokable;
use Yiisoft\EventDispatcher\Tests\Listener\NonStatic;
use Yiisoft\EventDispatcher\Tests\Listener\WithStaticMethod;
use function array_slice;
use function iterator_to_array;

final class ProviderTest extends TestCase
{
    public function testAttachCallableArray(): void
    {
        $listeners = (new ListenerCollection())
            ->add([WithStaticMethod::class, 'handle']);
        $provider = new Provider($listeners);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableFunction(): void
    {
        $listeners = (new ListenerCollection())
            ->add('Yiisoft\EventDispatcher\Tests\Provider\handle');
        $provider = new Provider($listeners);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachClosure(): void
    {
        $listeners = (new ListenerCollection())
            ->add(static function (Event $event) {
                // do nothing
            });

        $provider = new Provider($listeners);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableObject(): void
    {
        $listeners = (new ListenerCollection())
            ->add([new NonStatic(), 'handle']);

        $provider = new Provider($listeners);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testInvokable(): void
    {
        $listeners = (new ListenerCollection())
            ->add(new Invokable());

        $provider = new Provider($listeners);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned(): void
    {
        $listeners = (new ListenerCollection())
            ->add(static function (ParentInterface $parentInterface) {
                $parentInterface->register('parent interface');
            })
            ->add(static function (ParentClass $parentClass) {
                $parentClass->register('parent class');
            })
            ->add(static function (ClassInterface $classInterface) {
                $classInterface->register('class interface');
            })
            ->add(static function (ClassItself $classItself) {
                $classItself->register('class itself');
            });

        $provider = new Provider($listeners);

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

    public function testListenerForEventIsReturned(): void
    {
        $listener = fn () => null;

        $listeners = (new ListenerCollection())
            ->add($listener, Event::class);

        $provider = new Provider($listeners);
        $listeners = $provider->getListenersForEvent(new Event());

        $listeners = iterator_to_array($listeners, false);
        $this->assertCount(1, $listeners);
        $this->assertContains($listener, $listeners);
    }
}

function handle(Event $event): void
{
    // do nothing
}
