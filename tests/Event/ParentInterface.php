<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\Event;

interface ParentInterface
{
    public function register(string $name): void;

    public function registered(): array;
}
