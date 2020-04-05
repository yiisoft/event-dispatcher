<?php

namespace Yiisoft\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Provider\AbstractProviderConfigurator;
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
        $providerConfigurator = $this->getProviderConfigurator($provider);
        $providerConfigurator->attach([WithStaticMethod::class, 'handle']);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableFunction(): void
    {
        $provider = new Provider();
        $providerConfigurator = $this->getProviderConfigurator($provider);
        $providerConfigurator->attach('Yiisoft\EventDispatcher\Tests\Provider\handle');

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachClosure(): void
    {
        $provider = new Provider();
        $providerConfigurator = $this->getProviderConfigurator($provider);
        $providerConfigurator->attach(function (Event $event) {
            // do nothing
        });

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableObject(): void
    {
        $provider = new Provider();
        $providerConfigurator = $this->getProviderConfigurator($provider);
        $providerConfigurator->attach([new NonStatic(), 'handle']);

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testInvokable(): void
    {
        $provider = new Provider();
        $providerConfigurator = $this->getProviderConfigurator($provider);
        $providerConfigurator->attach(new Invokable());

        $listeners = $provider->getListenersForEvent(new Event());
        $this->assertCount(1, $listeners);
    }

    public function testListenersForClassHierarchyAreReturned(): void
    {
        $provider = new Provider();
        $providerConfigurator = $this->getProviderConfigurator($provider);

        $providerConfigurator->attach(function (ParentInterface $parentInterface) {
            $parentInterface->register('parent interface');
        });
        $providerConfigurator->attach(function (ParentClass $parentClass) {
            $parentClass->register('parent class');
        });
        $providerConfigurator->attach(function (ClassInterface $classInterface) {
            $classInterface->register('class interface');
        });
        $providerConfigurator->attach(function (ClassItself $classItself) {
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
        $providerConfigurator = $this->getProviderConfigurator($provider);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Listeners must declare an object type they can accept.');

        $providerConfigurator->attach(fn () => null);
    }

    public function testListenerForEventIsReturned(): void
    {
        $provider = new Provider();
        $providerConfigurator = $this->getProviderConfigurator($provider);

        $listener = fn () => null;

        $providerConfigurator->attach($listener, Event::class);

        $listeners = $provider->getListenersForEvent(new Event());

        $listeners = \iterator_to_array($listeners, false);
        $this->assertCount(1, $listeners);
        $this->assertContains($listener, $listeners);
    }

    public function testDetachListenersForEventAreDetached(): void
    {
        $provider = new Provider();
        $providerConfigurator = $this->getProviderConfigurator($provider);

        $providerConfigurator->attach(fn () => null, Event::class);
        $providerConfigurator->detach(Event::class);

        $listeners = $provider->getListenersForEvent(new Event());

        $this->assertCount(0, $listeners);
    }

    private function getProviderConfigurator(Provider $provider)
    {
        return new class($provider) extends AbstractProviderConfigurator {
            private Provider $provider;

            public function __construct(Provider $provider)
            {
                $this->provider = $provider;
            }

            public function attach(callable $listener, string $eventClassName = ''): void
            {
                $this->provider->attach($listener, $eventClassName);
            }

            public function detach(string $eventClassName): void
            {
                $this->provider->detach($eventClassName);
            }
        };
    }
}

function handle(Event $event): void
{
    // do nothing
}
