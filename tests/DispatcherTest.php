<?php
namespace Yiisoft\EventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Tests\Event\Event;
use Yiisoft\EventDispatcher\Tests\Event\StoppableEvent;

class DispatcherTest extends TestCase
{
    public function testCallsAllListeners()
    {
        $event = new Event();

        $provider = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (Event $event) {
                    $event->register(1);
                };
                yield function (Event $event) {
                    $event->register(2);
                };
                yield function (Event $event) {
                    $event->register(3);
                };
            }
        };

        $dispatcher = new Dispatcher($provider);
        $dispatcher->dispatch($event);

        $this->assertEquals([1, 2, 3], $event->registered());
    }

    public function testPropagationStops()
    {
        $event = new StoppableEvent();

        $provider = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function (StoppableEvent $event) {
                    $event->register(1);
                    $event->stopPropagation();
                };
                yield function (StoppableEvent $event) {
                    $event->register(2);
                };
                yield function (StoppableEvent $event) {
                    $event->register(3);
                };
            }
        };

        $dispatcher = new Dispatcher($provider);
        $dispatcher->dispatch($event);

        $this->assertEquals([1], $event->registered());
    }
}
