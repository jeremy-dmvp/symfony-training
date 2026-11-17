<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Messenger;

use Application\MessageBus\EventBus as EventBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventBus implements EventBusInterface
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public function dispatch(object $event): void
    {
        $this->messageBus->dispatch($event);
    }
}
