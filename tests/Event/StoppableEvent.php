<?php
namespace Yii\EventDispatcher\Tests\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent extends Event implements StoppableEventInterface
{
    private $isPropogationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->isPropogationStopped;
    }

    public function stopPropogation()
    {
        $this->isPropogationStopped = true;
    }
}
