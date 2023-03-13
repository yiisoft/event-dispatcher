<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Php8\Provider;

use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Tests\Event\ParentClass;
use Yiisoft\EventDispatcher\Tests\Event\StoppableEvent;

final class ListenerCollectionTest extends TestCase
{
    public function testAddCallableWithUnionType(): void
    {
        $listenerCollection = (new ListenerCollection())
            ->add(static function (StoppableEvent|ParentClass $var): void {
            });

        $this->assertCount(1, iterator_to_array($listenerCollection->getForEvents(StoppableEvent::class)));
        $this->assertCount(1, iterator_to_array($listenerCollection->getForEvents(ParentClass::class)));
    }
}
