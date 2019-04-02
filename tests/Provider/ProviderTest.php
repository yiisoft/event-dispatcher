<?php
namespace Yii\EventDispatcher\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Yii\EventDispatcher\Provider\Provider;
use Yii\EventDispatcher\Tests\EventListener;
use Yii\EventDispatcher\Tests\InvokableEventListener;
use Yii\EventDispatcher\Tests\CustomEvent;
use Yii\EventDispatcher\Tests\StaticEventListener;

class ProviderTest extends TestCase
{
    public function testAttachCallableArray()
    {
        $provider = new Provider();
        $provider->attach([StaticEventListener::class, 'handle']);

        $listeners = $provider->getListenersForEvent(new CustomEvent());
        $this->assertCount(1, $listeners);

    }

    public function testAttachCallableFunction()
    {
        $provider = new Provider();
        $provider->attach('Yii\EventDispatcher\Tests\Provider\handle');

        $listeners = $provider->getListenersForEvent(new CustomEvent());
        $this->assertCount(1, $listeners);
    }

    public function testAttachClosure()
    {
        $provider = new Provider();
        $provider->attach(function (CustomEvent $event) {});

        $listeners = $provider->getListenersForEvent(new CustomEvent());
        $this->assertCount(1, $listeners);
    }

    public function testAttachCallableObject()
    {
        $provider = new Provider();
        $provider->attach([new EventListener(), 'handle']);

        $listeners = $provider->getListenersForEvent(new CustomEvent());
        $this->assertCount(1, $listeners);
    }

    public function testInvokable()
    {
        $provider = new Provider();
        $provider->attach(new InvokableEventListener());

        $listeners = $provider->getListenersForEvent(new CustomEvent());
        $this->assertCount(1, $listeners);
    }
}

function handle(CustomEvent $event)
{
    // do nothing
}
