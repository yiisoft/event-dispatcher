<?php

namespace Yiisoft\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\DeferredProvider;
use Yiisoft\EventDispatcher\Tests\Event\Event;

class DeferredProviderTest extends TestCase
{
    public function testEventsDispatchAfterDispatchEventsMethodCall(): void
    {
        $deferProvider = $this->createDeferredProvider();
        $deferDispatcher = new Dispatcher($deferProvider);

        $deferProvider->deferEvents();

        $event = new Event();
        $deferDispatcher->dispatch($event);
        $deferDispatcher->dispatch($event);
        $deferDispatcher->dispatch($event);

        $this->assertEmpty($event->registered());
        $deferProvider->dispatchEvents();

        $this->assertCount(3, $event->registered());
    }

    public function testEventsDispatchImmediatelyBecauseDeferNotUsed(): void
    {
        $deferProvider = $this->createDeferredProvider();
        $deferDispatcher = new Dispatcher($deferProvider);

        $event = new Event();
        $deferDispatcher->dispatch($event);
        $deferDispatcher->dispatch($event);
        $deferDispatcher->dispatch($event);

        $this->assertCount(3, $event->registered());
    }

    public function testClearEvents(): void
    {
        $deferProvider = $this->createDeferredProvider();
        $deferDispatcher = new Dispatcher($deferProvider);

        $deferProvider->deferEvents();

        $event = new Event();
        $deferDispatcher->dispatch($event);
        $deferDispatcher->dispatch($event);
        $deferDispatcher->dispatch($event);

        $deferProvider->clearEvents();
        $deferProvider->dispatchEvents();

        $this->assertEmpty($event->registered());
    }

    private function createDeferredProvider(): DeferredProvider
    {
        return new DeferredProvider(new Dispatcher($this->getProvider()));
    }

    private function getProvider(): ListenerProviderInterface
    {
        return new class() implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield static function (Event $event) {
                    $event->register(1);
                };
            }
        };
    }
}
