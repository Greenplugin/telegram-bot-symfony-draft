<?php
declare(strict_types=1);

namespace App\Service;

use Buzz\Client\BuzzClientInterface;
use Greenplugin\TelegramBot\HttpClientInterface;
use Nyholm\Psr7\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class WebClient implements HttpClientInterface
{
    private $client;
    private $params;
    private $fs;
    private $longPooling = false;

    public function __construct(BuzzClientInterface $client, ParameterBagInterface $params, Filesystem $fs)
    {
        $this->client = $client;
        $this->params = $params;
        $this->fs = $fs;
    }

    /**
     * @param string $path
     * @return mixed
     * @throws \Psr\Http\Client\ClientException
     */
    public function get(string $path)
    {
        $request = new Request('GET', $path);

        $response = $this->client->sendRequest($request);

        return \json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $path
     * @param array $data
     * @return mixed
     * @throws \Psr\Http\Client\ClientException
     */
    public function post(string $path, array $data)
    {
        $this->checkDirs();
        $requestName = $this->getRequestName($path);
        $this->writeRequest($requestName, $data);
        $request = new Request('POST', $path, ['Content-Type' => 'application/json'], \json_encode($data));
        if ($this->longPooling) {
            $response = $this->client->sendRequest($request, ['timeout' => 16000]);
        } else {
            $response = $this->client->sendRequest($request);
        }
        $contents = $response->getBody()->getContents();
        $this->writeResponse($requestName, $contents);

        return \json_decode($contents);
    }

    public function enableLongPooling()
    {
        $this->longPooling = true;
    }

    public function disableLongPooling()
    {
        $this->longPooling = false;
    }

    private function writeRequest($requestName, array $data)
    {
        $fileName = $requestName . '_request.json';
        $this->fs->dumpFile($this->params->get('telegram.request_dir') . '/' . $fileName, json_encode($data));
    }

    private function writeResponse($requestName, string $data)
    {
        $fileName = $requestName . '_response.json';
        $this->fs->dumpFile($this->params->get('telegram.response_dir') . '/' . $fileName, $data);
    }

    private function getRequestName($path)
    {
        $exploded = explode('/', $path);
        return $exploded[count($exploded) - 1] . '_' . time();
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