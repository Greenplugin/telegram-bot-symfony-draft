<?php

namespace App\Command;

use Greenplugin\TelegramBot\BotApi;
use Greenplugin\TelegramBot\HttpClientInterface;
use Greenplugin\TelegramBot\Method\GetMeMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BotCommand extends Command
{
    protected static $defaultName = 'telegram:getMe';
    protected $params;
    protected $client;
    protected $bot;

    public function __construct(ParameterBagInterface $params, HttpClientInterface $client, $name = null)
    {
        $this->params = $params;
        $this->client = $client;
        $this->bot = new BotApi($this->client, $this->params->get('telegram.token'));
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('run telegram command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->bot->getMe(new GetMeMethod());

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
