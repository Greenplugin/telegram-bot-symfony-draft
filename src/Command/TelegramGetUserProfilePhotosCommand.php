<?php

namespace App\Command;

use Greenplugin\TelegramBot\Method\GetFileMethod;
use Greenplugin\TelegramBot\Method\GetUserProfilePhotosMethod;
use Greenplugin\TelegramBot\Type\PhotoSizeType;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TelegramGetUserProfilePhotosCommand extends BotCommand
{
    protected static $defaultName = 'telegram:getUserProfilePhotos';

    protected function configure()
    {
        $this->setDescription('Getting user profile photos.');
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

        $userId = $io->ask('user id', 0);

        if (!$userId) {
            $io->error('You must provide user id');
        }

        $getPhotosResponse = $this->bot->getUserProfilePhotos(new GetUserProfilePhotosMethod($userId));

        $io->title('total count of photos:' . $getPhotosResponse->totalCount);

        $photos = [];
        foreach ($getPhotosResponse->photos as $key => $photo) {
            /** @var PhotoSizeType $size */
            $fileId = $photo->toArray()[count($photo->toArray()) - 1]->fileId;
            $photos[] = [sprintf(
                "https://api.telegram.org/file/bot%s/%s",
                $this->params->get('telegram.token'),
                $this->bot->getFile(new GetFileMethod($fileId))->filePath
            )];
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
