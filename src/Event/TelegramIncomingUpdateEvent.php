<?php
declare(strict_types=1);

namespace App\Event;


use Greenplugin\TelegramBot\Type\UpdateType;
use Symfony\Component\EventDispatcher\Event;

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
    public function getUpdate()
    {
        return $this->update;
    }

}