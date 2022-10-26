<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Event Dispatcher</h1>
    <br>
</p>

[PSR-14](http://www.php-fig.org/psr/psr-14/) compatible event dispatcher provides an ability to dispatch events and listen
to events dispatched.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/event-dispatcher/v/stable.png)](https://packagist.org/packages/yiisoft/event-dispatcher)
[![Total Downloads](https://poser.pugx.org/yiisoft/event-dispatcher/downloads.png)](https://packagist.org/packages/yiisoft/event-dispatcher)
[![Build Status](https://github.com/yiisoft/event-dispatcher/workflows/build/badge.svg)](https://github.com/yiisoft/event-dispatcher/actions?query=workflow%3Abuild)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/event-dispatcher/badges/coverage.png)](https://scrutinizer-ci.com/g/yiisoft/event-dispatcher/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/event-dispatcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/event-dispatcher/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https://badge-api.stryker-mutator.io/github.com/yiisoft/event-dispatcher/master)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/event-dispatcher/master)
[![static analysis](https://github.com/yiisoft/event-dispatcher/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/event-dispatcher/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/event-dispatcher/coverage.svg)](https://shepherd.dev/github/yiisoft/event-dispatcher)

## Features

- [PSR-14](http://www.php-fig.org/psr/psr-14/) compatible.
- Simple and lightweight.
- Encourages designing event hierarchy.
- Can combine multiple event listener providers.

## Requirements

- PHP 8.0 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/event-dispatcher --prefer-dist
```

## General usage

The library consists of two parts: event dispatcher and event listener provider. Provider's job is to register listeners
for a certain event type. Dispatcher's job is to take an event, get listeners for it from a provider and call them sequentially.

```php
// Add some listeners.
$listeners = (new \Yiisoft\EventDispatcher\Provider\ListenerCollection())
    ->add(function (AfterDocumentProcessed $event) {
        $document = $event->getDocument();
        // Do something with document.
    });

$provider = new Yiisoft\EventDispatcher\Provider\Provider($listeners);
$dispatcher = new Yiisoft\EventDispatcher\Dispatcher\Dispatcher($provider);
```

The event dispatching may look like:

```php
use Psr\EventDispatcher\EventDispatcherInterface;

final class DocumentProcessor
{
    private EventDispatcherInterface $eventDispatcher;
    
    public function __construct(EventDispatcherInterface $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function process(Document $document): void
    {
        // Process the document, then dispatch completion event.
        $this->eventDispatcher->dispatch(new AfterDocumentProcessed($document));
    }
}
```

### Stoppable events

Event could be made stoppable by implementing `Psr\EventDispatcher\StoppableEventInterface`:

```php
final class BusyEvent implements Psr\EventDispatcher\StoppableEventInterface
{
    // ...

    public function isPropagationStopped(): bool
    {
        return true;
    }
}
```

This way we can ensure that only first event listener will be able to handle the event. Another option is
to allow stopping propagation in one of the listeners by providing corresponding event method.

### Events hierarchy

Events do not have any name or wildcard matching on purpose. Event class names and class/interface hierarchy
and composition could be used to achieve great flexibility:

```php
interface DocumentEvent
{
}

final class BeforeDocumentProcessed implements DocumentEvent
{
}

final class AfterDocumentProcessed implements DocumentEvent
{
}
```

With the interface above listening to all document-related events could be done as:


```php
$listeners->add(static function (DocumentEvent $event) {
    // log events here
});
```

### Combining multiple listener providers

In case you want to combine multiple listener providers, you can use `CompositeProvider`:

```php
$compositeProvider = new Yiisoft\EventDispatcher\Provider\CompositeProvider();
$provider = new Yiisoft\EventDispatcher\Provider\Provider($listeners);
$compositeProvider->add($provider);
$compositeProvider->add(new class implements ListenerProviderInterface {
    public function getListenersForEvent(object $event): iterable
    {
        yield static function ($event) {
            // handle 
        };
    }
});

$dispatcher = new Yiisoft\EventDispatcher\Dispatcher\Dispatcher($compositeProvider);
```

### Register listeners with concrete event names

You may use a more simple listener provider, which allows you to specify which event they can provide.

It can be useful in some specific cases, for instance if one of your listeners does not need the event
object passed as a parameter (can happen if the listener only needs to run at a specific stage during
runtime, but does not need event data).

In that case, it is advised to use the aggregate (see above) if you need features from both providers included
in this library.

```php
$listeners = (new \Yiisoft\EventDispatcher\Provider\ListenerCollection())
    ->add(static function () {
    // this function does not need an event object as argument
}, SomeEvent::class);
```

### Dispatching to multiple dispatchers

There may be a need to dispatch an event via multiple dispatchers at once. It could be achieved like the following:

```php
use Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Provider\Provider;

// Add some listeners.
$listeners1 = (new ListenerCollection())
    ->add(function (AfterDocumentProcessed $event) {
        $document = $event->getDocument();
        // Do something with document.
    });

$provider1 = new Provider($listeners1);

// Add some listeners.
$listeners2 = (new ListenerCollection())
    ->add(function (AfterDocumentProcessed $event) {
        $document = $event->getDocument();
        // Do something with document.
    });

$provider2 = new Provider($listeners2);


$dispatcher = new CompositeDispatcher();
$dispatcher->attach(new Dispatcher($provider1));
$dispatcher->attach(new Dispatcher($provider2));

$dispatcher->dispatch(new MyEvent());
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

Yii Event Dispatcher is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Credits

- Larry Garfield (@crell) for initial implementation of deriving callable parameter type.

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
