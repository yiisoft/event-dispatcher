<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent extends Event implements StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->isPropagationStopped = true;
    }
}
