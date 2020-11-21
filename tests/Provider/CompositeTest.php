<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\EventDispatcher\Provider\CompositeProvider;
use Yiisoft\EventDispatcher\Tests\Event\Event;

class CompositeTest extends TestCase
{
    public function testProvidesAllListeners(): void
    {
        $event = new Event();

        $provider1 = new class() implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield static function (Event $event) {
                    $event->register('1');
                };
            }
        };

        $provider2 = new class() implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield static function (Event $event) {
                    $event->register('2');
                };
            }
        };

        $aggregate = new CompositeProvider();

        $aggregate->attach($provider1);
        $aggregate->attach($provider2);

        foreach ($aggregate->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
        $this->assertEquals(['1', '2'], $event->registered());
    }
}
