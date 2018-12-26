<?php

namespace App\Command;

use Greenplugin\TelegramBot\BotApi;
use Greenplugin\TelegramBot\HttpClientInterface;
use Greenplugin\TelegramBot\Method\GetMeMethod;
use Greenplugin\TelegramBot\Method\GetUpdatesMethod;
use Greenplugin\TelegramBot\Type\UpdateType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class GetTelegramUpdatesCommand
 * @package App\Command
 */
class GetTelegramUpdatesCommand extends Command
{
    protected static $defaultName = 'telegram:showUpdates';
    private $params;
    private $client;
    private $bot;

    public function __construct(ParameterBagInterface $params, HttpClientInterface $client)
    {
        $this->params = $params;
        $this->client = $client;
        $this->bot = new BotApi($this->client, $this->params->get('telegram.token'));
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
     * @throws \Greenplugin\TelegramBot\Exception\BadArgumentException
     * @throws \Greenplugin\TelegramBot\Exception\ResponseException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);


        $updates = $this->bot->getUpdates(new GetUpdatesMethod([]));

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
     * @return \Greenplugin\TelegramBot\Type\MessageType|null
     */
    private function getUpdateData($update)
    {
        if (isset($update->message)) {
            return $update->message;
        }
        if (isset($update->editedMessage)) {
            return $update->editedMessage;
        }
    }
}
