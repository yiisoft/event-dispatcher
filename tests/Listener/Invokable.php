<?php
namespace Yii\EventDispatcher\Tests\Listener;

use Yii\EventDispatcher\Tests\Event\Event;

class Invokable
{
    public function __invoke(Event $event)
    {
        // do nothing
    }
}
