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
        $deferredProvider = $this->createDeferredProvider();
        $deferredDispatcher = new Dispatcher($deferredProvider);

        $deferredProvider->deferEvents();

        $event = new Event();
        $deferredDispatcher->dispatch($event);
        $deferredDispatcher->dispatch($event);
        $deferredDispatcher->dispatch($event);

        $this->assertEmpty($event->registered());

        $result = $deferredProvider->dispatchEvents();

        $this->assertCount(3, $result);
        $this->assertCount(3, $event->registered());
    }

    public function testEventsDispatchImmediatelyBecauseDeferNotUsed(): void
    {
        $deferredProvider = $this->createDeferredProvider();
        $deferredDispatcher = new Dispatcher($deferredProvider);

        $event = new Event();
        $deferredDispatcher->dispatch($event);
        $deferredDispatcher->dispatch($event);
        $deferredDispatcher->dispatch($event);

        $this->assertCount(3, $event->registered());
    }

    public function testClearEvents(): void
    {
        $deferredProvider = $this->createDeferredProvider();
        $deferredDispatcher = new Dispatcher($deferredProvider);

        $deferredProvider->deferEvents();

        $event = new Event();
        $deferredDispatcher->dispatch($event);
        $deferredDispatcher->dispatch($event);
        $deferredDispatcher->dispatch($event);

        $deferredProvider->clearEvents();

        $result = $deferredProvider->dispatchEvents();

        $this->assertEmpty($result);
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
