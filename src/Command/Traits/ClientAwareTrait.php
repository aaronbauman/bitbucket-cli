<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command\Traits;

use GuzzleHttp\Client;
use Webmozart\Json\JsonDecoder;

/**
 * Trait ClientAwareTrait
 * @package Martiis\BitbucketCli\Command\Traits
 */
trait ClientAwareTrait
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var JsonDecoder
     */
    private static $decoder;

    /**
     * @required
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return array
     */
    public function requestGetJson(string $uri, array $options): array
    {
        return $this->getJsonDecoder()->decode(
            $this->client->get($uri, $options)->getBody()->getContents()
        );
    }

    /**
     * @return JsonDecoder
     */
    protected function getJsonDecoder()
    {
        if (!static::$decoder) {
            static::$decoder = new JsonDecoder();
            static::$decoder->setObjectDecoding(JsonDecoder::ASSOC_ARRAY);
        }

        return static::$decoder;
    }
}
