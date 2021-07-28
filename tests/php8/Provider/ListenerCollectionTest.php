<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Php8\Provider;

use PHPUnit\Framework\TestCase;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;

final class ListenerCollectionTest extends TestCase
{
    public function testAddCallableWithUnionType(): void
    {
        $listenerCollection = (new ListenerCollection())
            ->add(static function (string|int $var): void {
            });

        $this->assertCount(1, $listenerCollection->getForEvents('string|int'));
    }
}
