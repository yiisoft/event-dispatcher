<?php
namespace Yii\EventDispatcher\Tests;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
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

    public function testPropogationStops()
    {
        $event = new class implements StoppableEventInterface {
            public $listeners = [];

            private $isPropogationStopped = false;

            public function isPropagationStopped(): bool
            {
                return $this->isPropogationStopped;
            }

            public function stopPropogation(): void
            {
                $this->isPropogationStopped = true;
            }
        };

        $provider = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function ($event) {
                    $event->listeners[] = 1;
                    $event->stopPropogation();
                };
                yield function ($event) { $event->listeners[] = 2; };
                yield function ($event) { $event->listeners[] = 3; };
            }
        };

        $dispatcher = new Dispatcher($provider);
        $dispatcher->dispatch($event);

        $this->assertEquals([1], $event->listeners);
    }
}
