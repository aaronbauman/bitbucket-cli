<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Webmozart\Json\JsonDecoder;

class GuzzleBitbucketClient implements BitbucketClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var JsonDecoder
     */
    private static $decoder;

    /**
     * GuzzleBitbucketClient constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getTeamList(string $role, array $options = [])
    {
        $response = $this->client->request(
            'GET',
            str_replace(self::VARS_TEAM_LIST, [self::VERSION], self::URI_TEAM_LIST),
            [
                RequestOptions::QUERY => [
                    'role' => $role,
                    'page' => (int) $options['page'] ?? 1,
                ],
            ]
        );

        return $this->getJsonDecoder()->decode($response->getBody()->getContents());
    }

    /**
     * {@inheritdoc}
     */
    public function getTeamProjectList(string $owner, array $options = [])
    {
        $response = $this->client->request(
            'GET',
            str_replace(
                self::VARS_TEAM_PROJECT_LIST,
                [self::VERSION, $owner],
                self::URI_TEAM_PROJECT_LIST
            ),
            [
                RequestOptions::QUERY => [
                    'page' => (int) $options['page'] ?? 1,
                ],
            ]
        );

        return $this->getJsonDecoder()->decode($response->getBody()->getContents());
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryList(string $username, array $options = [])
    {
        $response = $this->client->request(
            'GET',
            str_replace(
                self::VARS_REPOSITORY_LIST,
                [self::VERSION, $username],
                self::URI_REPOSITORY_LIST
            ),
            [
                RequestOptions::QUERY => array_replace(
                    ['page' => 1],
                    array_diff_key($options, array_flip(['page', 'role', 'q']))
                ),
            ]
        );

        return $this->getJsonDecoder()->decode($response->getBody()->getContents());
    }

    /**
     * {@inheritdoc}
     */
    public function getPullRequestList(string $username, string $repoSlug, array $options = [])
    {
        $response =  $this->client->request(
            'GET',
            str_replace(
                self::VARS_PULL_REQUEST_LIST,
                [self::VERSION, $username, $repoSlug],
                self::URI_PULL_REQUEST_LIST
            ),
            [
                RequestOptions::QUERY => array_replace(
                    ['page' => 1],
                    array_diff_key($options, array_flip(['page', 'q']))
                ),
            ]
        );

        return $this->getJsonDecoder()->decode($response->getBody()->getContents());
    }

    /**
     * {@inheritdoc}
     */
    public function getPullRequest(string $username, string $repoSlug, int $pullRequestId, array $options = [])
    {
        $response =  $this->client->request(
            'GET',
            str_replace(
                self::VARS_PULL_REQUEST,
                [self::VERSION, $username, $repoSlug, $pullRequestId],
                self::URI_PULL_REQUEST
            )
        );

        return $this->getJsonDecoder()->decode($response->getBody()->getContents());
    }

    /**
     * @return JsonDecoder
     */
    protected function getJsonDecoder()
    {
        if (!self::$decoder) {
            self::$decoder = new JsonDecoder();
            self::$decoder->setObjectDecoding(JsonDecoder::ASSOC_ARRAY);
        }

        return self::$decoder;
    }
}
