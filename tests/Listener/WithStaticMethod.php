<?php
namespace Yiisoft\EventDispatcher\Tests\Listener;

use Yiisoft\EventDispatcher\Tests\Event\Event;

class WithStaticMethod
{
    public static function handle(Event $event)
    {
        // do nothing
    }
}
