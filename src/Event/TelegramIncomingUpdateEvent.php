<?php
declare(strict_types=1);

namespace App\Event;


use Symfony\Component\EventDispatcher\Event;
use TgBotApi\BotApiBase\Type\UpdateType;

/**
 * Class TelegramIncomingUpdateEvent
 * @package App\Event
 */
class TelegramIncomingUpdateEvent extends Event
{

    const NAME = 'telegram.update.income';

    /**
     * @var UpdateType
     */
    protected $update;

    /**
     * TelegramIncomingUpdateEvent constructor.
     * @param UpdateType $update
     */
    public function __construct(UpdateType $update)
    {
        $this->update = $update;
    }

    /**
     * @return UpdateType
     */
    public function getUpdate(): UpdateType
    {
        return $this->update;
    }

}