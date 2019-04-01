<?php
namespace Yii\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yii\EventDispatcher\Provider\Aggregate;

class AggregateTest extends TestCase
{
    public function test(): void
    {
        $event = new class {
            public $listeners = [];
        };

        $provider1 = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function ($event) { $event->listeners[] = 1; };
            }
        };

        $provider2 = new class implements ListenerProviderInterface {
            public function getListenersForEvent(object $event): iterable
            {
                yield function ($event) { $event->listeners[] = 2; };
            }
        };

        $aggregate = new Aggregate();

        $aggregate->addProvider($provider1);
        $aggregate->addProvider($provider2);

        foreach ($aggregate->getListenersForEvent($event) as $listener) {
            $listener($event);
        }
        $this->assertEquals([1, 2], $event->listeners);
    }
}
