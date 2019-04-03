<?php


namespace Yii\EventDispatcher\Tests\Event;


interface ParentInterface
{
    public function register(string $name): void;
    public function registered(): array;
}