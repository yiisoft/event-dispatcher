<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Tests\DeferredEventsTrait;

use Yiisoft\EventDispatcher\DeferredEventsTrait;

final class Entity
{
    use DeferredEventsTrait;

    private string $body = '';

    public function __construct()
    {
        $this->recordEvent(new EntityCreated());
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function changeBody(string $body): void
    {
        $this->body = $body;

        $this->recordEvent(new EntityModified());
    }
}
