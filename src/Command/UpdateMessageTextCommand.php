<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TgBotApi\BotApiBase\Method\EditMessageTextMethod;
use TgBotApi\BotApiBase\Method\SendLocationMethod;
use TgBotApi\BotApiBase\Method\SendMediaGroupMethod;
use TgBotApi\BotApiBase\Method\SendMessageMethod;
use TgBotApi\BotApiBase\Type\InputFileType;
use TgBotApi\BotApiBase\Type\InputMedia\InputMediaPhotoType;

class UpdateMessageTextCommand extends BotCommand
{
    protected static $defaultName = 'telegram:update_message_text';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->bot->send(
            SendMessageMethod::create($this->params->get('chat.default'), 'Hi2')
        );

        sleep(5);

        $prevMessageId = $response->messageId;
        $result = $this->bot->edit(
            EditMessageTextMethod::create($this->params->get('chat.default'), $prevMessageId, 'Hello')
        );

        var_dump($result);

        $rows = [];

        $rows[] = [
            $result->messageId,
            $result->chat->title,
            $result->chat->id,
            $result->chat->firstName,
        ];

        $io->table([
            'id',
            'chat title',
            'chat id',
            'chat firstName'
        ], $rows);
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
