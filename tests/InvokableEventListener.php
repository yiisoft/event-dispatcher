<?php


namespace Yii\EventDispatcher\Tests;


class InvokableEventListener
{
    public function __invoke(CustomEvent $event)
    {
        // do nothing
    }
}
