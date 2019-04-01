<?php


namespace Yii\EventDispatcher\Tests;


class InvokeableEventListener
{
    public function __invoke(CustomEvent $event)
    {
        // do nothing
    }
}
