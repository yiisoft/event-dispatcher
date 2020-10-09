<?php

namespace Yiisoft\EventDispatcher\Tests\Dispatcher;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher\DeferredDispatcher;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Tests\Event\Event;

final class DeferredDispatcherTest extends TestCase
{
    public function testEventIsNotDispatchedImmediately(): void
    {
        $deferredDispatcher = $this->getDeferredDispatcher();

        $event = new Event();
        $deferredDispatcher->dispatch($event);

        $this->assertEquals([], $event->registered());
    }

    public function testFlushDispatchesAllEventsOnce(): void
    {
        $deferredDispatcher = $this->getDeferredDispatcher();

        $event1 = new Event();
        $event2 = new Event();

        $deferredDispatcher->dispatch($event1);
        $deferredDispatcher->dispatch($event2);

        $deferredDispatcher->flush();
        $deferredDispatcher->flush();

        $this->assertEquals(['triggered'], $event1->registered());
        $this->assertEquals(['triggered'], $event2->registered());
    }

    private function getDeferredDispatcher(): DeferredDispatcher
    {
        $provider = new class() implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (Event $event) {
                    $event->register('triggered');
                };
            }
        };

        $dispatcher = new Dispatcher($provider);
        return new DeferredDispatcher($dispatcher);
    }
}
