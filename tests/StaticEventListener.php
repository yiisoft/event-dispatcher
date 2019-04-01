<?php


namespace Yii\EventDispatcher\Tests;


class StaticEventListener
{
    public static function handle(CustomEvent $event)
    {
        // do nothing
    }
}
