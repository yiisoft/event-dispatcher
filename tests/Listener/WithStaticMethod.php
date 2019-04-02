<?php
namespace Yii\EventDispatcher\Tests\Listener;

use Yii\EventDispatcher\Tests\Event\Event;

class WithStaticMethod
{
    public static function handle(Event $event)
    {
        // do nothing
    }
}
