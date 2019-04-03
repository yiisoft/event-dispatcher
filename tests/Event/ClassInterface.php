<?php


namespace Yii\EventDispatcher\Tests\Event;


interface ClassInterface
{
    public function register(string $name): void;
    public function registered(): array;
}