<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use TgBotApi\BotApiBase\ApiClient;
use TgBotApi\BotApiBase\BotApiRequestInterface;

class WebClient extends ApiClient
{
    private $params;
    private $fs;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        ClientInterface $client,
        ParameterBagInterface $params,
        Filesystem $fs
    ) {
        parent::__construct($requestFactory, $streamFactory, $client);
        $this->params = $params;
        $this->fs = $fs;
        $this->checkDirs();
    }

    public function send(string $method, BotApiRequestInterface $request)
    {
        $this->writeRequest($method, $request);
        $response = parent::send($method, $request);
        $this->writeResponse($method, $response);
        return $response;
    }

    private function writeRequest($requestName, BotApiRequestInterface $request)
    {
        $fileName = $requestName . '_request.json';
        $this->fs->dumpFile($this->params->get('telegram.request_dir') . '/' . $fileName, json_encode($request->getData()));
    }

    private function writeResponse($requestName, $data)
    {
        $fileName = $requestName . '_response.json';
        $this->fs->dumpFile($this->params->get('telegram.response_dir') . '/' . $fileName, json_encode($data));
    }

    private function checkDirs()
    {
        if (!$this->fs->exists($this->params->get('telegram.request_dir'))) {
            $this->fs->mkdir($this->params->get('telegram.request_dir'));
        }
        if (!$this->fs->exists($this->params->get('telegram.response_dir'))) {
            $this->fs->mkdir($this->params->get('telegram.response_dir'));
        }
    }
}