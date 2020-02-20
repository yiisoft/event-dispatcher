<?php

namespace Yiisoft\EventDispatcher\Tests\Dispatcher;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher;

class CompositeDispatcherTest extends TestCase
{
    public function testCallsAllDispatchers(): void
    {
        $dispatcher1 = $this->createTransparentDispatcher();
        $dispatcher2 = $this->createTransparentDispatcher();

        $event = new \stdClass();
        $compositeDispatcher = new CompositeDispatcher();
        $compositeDispatcher->attach($dispatcher1);
        $compositeDispatcher->attach($dispatcher2);

        $result = $compositeDispatcher->dispatch($event);

        $this->assertSame($event, $result);
    }

    public function testPropagationStops(): void
    {
        $notStoppableEvent = $this->createMock(StoppableEventInterface::class);
        $notStoppableEvent
            ->method('isPropagationStopped')
            ->willReturn(false);

        $stoppableEvent = $this->createMock(StoppableEventInterface::class);
        $stoppableEvent
            ->method('isPropagationStopped')
            ->willReturn(true);

        $dispatcher1 = $this->createMock(EventDispatcherInterface::class);
        $dispatcher1
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($stoppableEvent);

        $dispatcher2 = $this->createMock(EventDispatcherInterface::class);
        $dispatcher2
            ->expects($this->never())
            ->method('dispatch')
            ->willReturnArgument(0);

        $compositeDispatcher = new CompositeDispatcher();
        $compositeDispatcher->attach($dispatcher1);
        $compositeDispatcher->attach($dispatcher2);

        $result = $compositeDispatcher->dispatch($notStoppableEvent);

        $this->assertSame($stoppableEvent, $result);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Psr\EventDispatcher\EventDispatcherInterface
     */
    private function createTransparentDispatcher()
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnArgument(0);

        return $dispatcher;
    }
}
