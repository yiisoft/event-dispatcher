<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Listener;

use Yiisoft\EventDispatcher\Tests\Event\Event;

class WithStaticMethod
{
    public static function handle(Event $event): void
    {
        // do nothing
    }
}
