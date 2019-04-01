<?php
namespace Yii\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

final class Aggregate implements ListenerProviderInterface
{
    /**
     * @var ListenerProviderInterface[]
     */
    private $providers;

    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->providers as $provider) {
            yield from $provider->getListenersForEvent($event);
        }
    }

   public function attach(ListenerProviderInterface $provider): void
   {
       $this->providers[] = $provider;
   }
}
