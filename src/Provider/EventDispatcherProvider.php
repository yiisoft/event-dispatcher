<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Provider;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;

final class EventDispatcherProvider extends ServiceProvider
{
    /**
     * @suppress PhanAccessMethodProtected
     */
    public function register(Container $container): void
    {
        $container->set(ListenerProviderInterface::class, Provider::class);
        $container->set(EventDispatcherInterface::class, Dispatcher::class);
    }
}
