<?php

namespace Yiisoft\EventDispatcher\Tests\Event;

class Event
{
    /**
     * @var string[]
     */
    private array $registered = [];

    public function register(string $name): void
    {
        $this->registered[] = $name;
    }

    /**
     * @return string[]
     */
    public function registered(): array
    {
        return $this->registered;
    }
}
