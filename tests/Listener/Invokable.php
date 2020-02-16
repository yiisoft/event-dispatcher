<?php

namespace Yiisoft\EventDispatcher\Tests\Listener;

use Yiisoft\EventDispatcher\Tests\Event\Event;

class Invokable
{
    public function __invoke(Event $event): void
    {
        // do nothing
    }
}
