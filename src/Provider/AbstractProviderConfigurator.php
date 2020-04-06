<?php

namespace Yiisoft\EventDispatcher\Provider;

abstract class AbstractProviderConfigurator
{
    protected function attach(callable $listener, string $eventClassName = ''): void
    {
        throw new \RuntimeException("Method 'attach' does not exist.");
    }
}
