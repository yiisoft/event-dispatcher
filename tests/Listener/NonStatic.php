<?php
namespace Yii\EventDispatcher\Tests\Listener;

use Yii\EventDispatcher\Tests\Event\Event;

class NonStatic
{
    public function handle(Event $event)
    {
        // do nothing
    }
}