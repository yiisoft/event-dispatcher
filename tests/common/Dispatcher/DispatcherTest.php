<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Dispatcher;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Tests\Event\Event;
use Yiisoft\EventDispatcher\Tests\Event\StoppableEvent;

class DispatcherTest extends TestCase
{
    public function testCallsAllListeners(): void
    {
        $event = new Event();

        $provider = new class () implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield static function (Event $event) {
                    $event->register('1');
                };
                yield static function (Event $event) {
                    $event->register('2');
                };
                yield static function (Event $event) {
                    $event->register('3');
                };
            }
        };

        $dispatcher = new Dispatcher($provider);
        $dispatcher->dispatch($event);

        $this->assertEquals(['1', '2', '3'], $event->registered());
    }

    public function testPropagationStops(): void
    {
        $event = new StoppableEvent();

        $provider = new class () implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield static function (StoppableEvent $event) {
                    $event->register('1');
                    $event->stopPropagation();
                };
                yield static function (StoppableEvent $event) {
                    $event->register('2');
                };
                yield static function (StoppableEvent $event) {
                    $event->register('3');
                };
            }
        };

        $dispatcher = new Dispatcher($provider);
        $dispatcher->dispatch($event);

        $this->assertEquals(['1'], $event->registered());
    }

    public function testEventSpoofing(): void
    {
        $event = new Event();
        $provider = new class () implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield static function (Event $event) {
                    $event->register('1');
                };
                yield static function (Event &$event) {
                    $event = new Event();
                };
                yield static function (Event $event) {
                    $event->register('2');
                };
            }
        };

        $dispatcher = new Dispatcher($provider);
        $dispatcher->dispatch($event);

        $this->assertEquals(['1', '2'], $event->registered());
    }
}
