<?php

declare(strict_types=1);

namespace Yiisoft\EventDispatcher\Support;

use InvalidArgumentException;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class InvalidEventConfigurationFormatException extends InvalidArgumentException implements FriendlyExceptionInterface
{
    public function getName(): string
    {
        return 'Configuration format passed to EventConfigurator is invalid.';
    }

    public function getSolution(): ?string
    {
        return <<<'SOLUTION'
            EventConfigurator accepts an array in the following format:
                [
                    'eventClassName1' => [$listener1, $listener2, ...],
                    'eventClassName2' => [$listener3, $listener4, ...],
                    ...
                ]
        SOLUTION;
    }
}
