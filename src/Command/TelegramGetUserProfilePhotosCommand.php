<?php

namespace App\Command;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TgBotApi\BotApiBase\Method\GetFileMethod;
use TgBotApi\BotApiBase\Method\GetUserProfilePhotosMethod;
use TgBotApi\BotApiBase\Type\PhotoSizeType;

class TelegramGetUserProfilePhotosCommand extends BotCommand
{
    protected static $defaultName = 'telegram:getUserProfilePhotos';

    protected function configure()
    {
        $this->setDescription('Getting user profile photos.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $userId = $io->ask('user id', 0);

        if (!$userId) {
            $io->error('You must provide user id');
        }

        $getPhotosResponse = $this->bot->getUserProfilePhotos(GetUserProfilePhotosMethod::create($userId));

        $io->title('total count of photos:' . $getPhotosResponse->totalCount);

        $photos = [];
        foreach ($getPhotosResponse->photos as $key => $photo) {
            $fileId = $photo[count($photo) - 1]->fileId;
            $photos[] = [sprintf(
                "https://api.telegram.org/file/bot%s/%s",
                $this->params->get('telegram.token'),
                $this->bot->getFile(GetFileMethod::create($fileId))->filePath
            )];
            /** @var PhotoSizeType $size */
            foreach ($photo as $size) {
                $photos[] = [$size->fileId, $size->fileSize, $size->width, $size->height];
            }
            if ($key < count($getPhotosResponse->photos) - 1) {
                $photos[] = new TableSeparator();
            }
        }

        $io->table(['fileId', 'fileSize', 'width', 'height'], $photos);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
