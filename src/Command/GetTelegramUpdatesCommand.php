<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TgBotApi\BotApiBase\BotApi;
use TgBotApi\BotApiBase\BotApiInterface;
use TgBotApi\BotApiBase\Method\GetUpdatesMethod;
use TgBotApi\BotApiBase\Type\UpdateType;

/**
 * Class GetTelegramUpdatesCommand
 * @package App\Command
 */
class GetTelegramUpdatesCommand extends Command
{
    protected static $defaultName = 'telegram:showUpdates';
    /**
     * @var BotApi
     */
    private $bot;

    public function __construct(BotApiInterface $bot)
    {
        $this->bot = $bot;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Get updates from Telegram bot');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \TgBotApi\BotApiBase\Exception\ResponseException
     * @throws \TgBotApi\BotApiBase\Exception\BadArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);


        $updates = $this->bot->getUpdates(GetUpdatesMethod::create());

        $io->title(sprintf("count of updates: %s", count($updates)));
        $rows = [];
        foreach ($updates as $update) {
            $rows[] = [
                $update->updateId,
                get_class($update),
                $this->getUpdateData($update)->text,
                $this->getUpdateData($update)->messageId,
                $this->getUpdateData($update)->chat->id
            ];
        }
        $io->table([
            'id',
            'type',
            'message',
            'messageId',
            'chatId'
        ], $rows);
    }


    /**
     * @param UpdateType $update
     * @return \TgBotApi\BotApiBase\Type\MessageType|null
     */
    private function getUpdateData(UpdateType $update)
    {
        if (isset($update->message)) {
            return $update->message;
        }
        if (isset($update->editedMessage)) {
            return $update->editedMessage;
        }
        return $update->message;
    }
}
