<?php

namespace Yiisoft\EventDispatcher\Tests\Listener;

use Yiisoft\EventDispatcher\Tests\Event\Event;

class NonStatic
{
    public function handle(Event $event)
    {
        // do nothing
    }
}
