<?php

namespace App\EventSubscriber;

use App\Event\TelegramIncomingUpdateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TelegramMessageSubscriber implements EventSubscriberInterface
{
    public function onTelegramUpdateIncome(TelegramIncomingUpdateEvent $event)
    {
        var_dump($event);
    }

    public static function getSubscribedEvents()
    {
        return [
            'telegram.update.income' => 'onTelegramUpdateIncome',
        ];
    }
}
