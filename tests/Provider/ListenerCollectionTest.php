<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Provider;

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
}
