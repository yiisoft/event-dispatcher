<?php

declare(strict_types=1);

use Yiisoft\Composer\Config\Builder;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Support\ListenerCollectionFactory as Factory;

return [
    ListenerCollection::class => static fn (Factory $factory) => $factory->create(require Builder::path('events-console')),
];
