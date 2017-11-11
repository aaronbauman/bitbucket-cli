<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command\Traits;

use GuzzleHttp\Client;

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
     * @required
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
