<?php
namespace Yii\EventDispatcher\Tests\Event;

class Event
{
    private $registered = [];

    public function register(string $name): void
    {
        $this->registered[] = $name;
    }

    public function registered(): array
    {
        return $this->registered;
    }
}
