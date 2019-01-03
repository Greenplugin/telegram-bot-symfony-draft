<?php

namespace App\Command;

use Greenplugin\TelegramBot\BotApi;
use Greenplugin\TelegramBot\BotApiInterface;
use Greenplugin\TelegramBot\Method\GetMeMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BotCommand extends Command
{
    protected static $defaultName = 'telegram:getMe';
    protected $client;
    /**
     * @var BotApi
     */
    protected $bot;
    protected $params;

    public function __construct(BotApiInterface $bot, ParameterBagInterface $params, $name = null)
    {
        $this->bot = $bot;
        $this->params = $params;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('run telegram command');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Greenplugin\TelegramBot\Exception\ResponseException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->bot->getMe(GetMeMethod::create());

        $io->table([
            'id',
            'username',
            'firstName',
            'lastName',
            'isBot',
            'languageCode'
        ], [[
            $result->id,
            $result->username,
            $result->firstName,
            $result->lastName,
            $result->isBot,
            $result->languageCode
        ]]);
    }
}
