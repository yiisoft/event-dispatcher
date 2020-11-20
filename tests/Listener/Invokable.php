<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Listener;

use Yiisoft\EventDispatcher\Tests\Event\Event;

class Invokable
{
    public function __invoke(Event $event): void
    {
        // do nothing
    }
}
