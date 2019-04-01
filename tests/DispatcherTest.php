<?php
namespace Yii\EventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yii\EventDispatcher\Dispatcher;

class DispatcherTest extends TestCase
{
    public function testCallsAllListeners()
    {
        $event = new class {
            public $listeners = [];
        };

        $provider = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function ($event) { $event->listeners[] = 1; };
                yield function ($event) { $event->listeners[] = 2; };
                yield function ($event) { $event->listeners[] = 3; };
            }
        };

        $dispatcher = new Dispatcher($provider);
        $dispatcher->dispatch($event);

        $this->assertEquals([1, 2, 3], $event->listeners);
    }
}