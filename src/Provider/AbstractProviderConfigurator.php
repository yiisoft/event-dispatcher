<?php

namespace Yiisoft\EventDispatcher\Provider;

class AbstractProviderConfigurator
{
    protected function attach(callable $listener, string $eventClassName = ''): void
    {
        throw new \RuntimeException("Method 'attach' does not exist.");
    }

    public function detach(string $eventClassName): void
    {
        throw new \RuntimeException("Method 'detach' does not exist.");
    }
}
