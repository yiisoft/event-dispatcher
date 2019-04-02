<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii Event Dispatcher</h1>
    <br>
</p>

[PSR-14](http://www.php-fig.org/psr/psr-14/) compatible event dispatcher provides an ability to dispatch events and listen
to events dispatched.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/event-dispatcher/v/stable.png)](https://packagist.org/packages/yiisoft/event-dispatcher)
[![Total Downloads](https://poser.pugx.org/yiisoft/event-dispatcher/downloads.png)](https://packagist.org/packages/yiisoft/event-dispatcher)
[![Build Status](https://travis-ci.org/yiisoft/event-dispatcher.svg?branch=master)](https://travis-ci.org/yiisoft/event-dispatcher)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/event-dispatcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/event-dispatcher/?branch=master)

## Features

- [PSR-14](http://www.php-fig.org/psr/psr-14/) compatible.
- Simple and lightweight.
- Encourages designing event hierarchy.
- Can combine mutliple event listener providers.

## General usage

The library consists of two parts: event dispatcher and event listener provider. Provider's job is to register listeners
for a certain event type. Dispatcher's job is to take an event, get a listeners for it from a provider and call them sequentially.

```php
$provider = new Yii\EventDispatcher\Provider\Provider();
$dispatcher = new Yii\EventDispatcher\Dispatcher($provider);

// adding some listeners
$provider->attach(function (AfterDocumentProcessed $event) {
    $document = $event->getDocument();
    // do something with document
});
```

The event dispatching may look like:

```php
class DocumentProcessor
{
    public function process(Document $document)
    {
        // process the docuemnt
        $dispatcher->dispatch(new AfterDocumentProcessed($document));
    }
}
```

## Stoppable events

Event could be made stoppable by implementing `Psr\EventDispatcher\StoppableEventInterface`:

```php
class BusyEvent implements Psr\EventDispatcher\StoppableEventInterface
{
    // ...

    public function isPropagationStopped(): bool
    {
        return true;
    }
}
```

This way we can ensure that only first event listener will be able to handle the event. Another option is
to allow stopping propogation in one of the listeners by providing corresponding event method.

## Events hierarchy

Events do not have any name or wildcard matching on purpose. Event class names and class/interface hierarchy
and composition could be used to achieve great flexibility:

```php
interface DocumentEvent
{
}

class BeforeDocumentProcessed implements DocumentEvent
{
}

class AfterDocumentProcessed implements DocumentEvent
{
}
```

With the interface above listening to all document-related events could be done as:


```php
$provider->attach(function (DocumentEvent $event) {
    // log events here
});
```

## Combining multiple listener providers

In case you want to combine multiple listener providers, you can use `Aggregate`:

```php
$aggregate = new Yii\EventDispatcher\Provider\Aggregate();
$provider1 = new Yii\EventDispatcher\Provider\Provider();
$aggregate->attach($provider1);
$aggregate->attach(new class implements ListenerProviderInterface {
    public function getListenersForEvent(object $event): iterable
    {
        yield function ($event) {
            // handle 
        };
    }
});

$dispatcher = new Yii\EventDispatcher\Dispatcher($aggregate);
```

## Credits

- Larry Garfield (@crell) for initial implementation of deriving callable parameter type.
