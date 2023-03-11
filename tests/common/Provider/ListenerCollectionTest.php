<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Provider;

use InvalidArgumentException;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Tests\Event\Event;

class ListenerCollectionTest extends TestCase
{
    public function testAddIsImmutable(): void
    {
        $listenerCollection = new ListenerCollection();
        $newInstance = $listenerCollection->add(static function () {
        }, Event::class);
        $this->assertNotSame($newInstance, $listenerCollection);
    }

    public function testAdd(): void
    {
        $listenerCollection = (new ListenerCollection())
            ->add(static function (string $var): void {
            });

        $this->assertCount(1, iterator_to_array($listenerCollection->getForEvents('string')));
    }

    public function testAddCallableWithoutParameter(): void
    {
        $listenerCollection = new ListenerCollection();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Listeners must accept an event object.');
        $listenerCollection->add(static function (): void {
        });
    }

    public function testAddCallableWithParameterWithoutTypeHint(): void
    {
        $listenerCollection = new ListenerCollection();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Listeners must declare an object type they can accept.');
        $listenerCollection->add(static function ($var): void {
        });
    }
}
