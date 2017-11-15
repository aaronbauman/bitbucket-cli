<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Client;

use GuzzleHttp\ClientInterface;
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
        $response = $this->client->request('GET', '/2.0/teams', [
            'query' => [
                'role' => $role,
                'page' => (int) $options['page'] ?? 1,
            ]
        ]);

        return $this->getJsonDecoder()->decode($response->getBody()->getContents());
    }

    /**
     * {@inheritdoc}
     */
    public function getTeamProjectList(string $owner, array $options = [])
    {
        $response = $this->client->request(
            'GET',
            str_replace('{owner}', $owner, '/2.0/teams/{owner}/projects/'),
            [
                'query' => [
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
            str_replace('{username}', $username, '/2.0/repositories/{username}'),
            [
                'query' => array_replace(
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
                ['{username}', '{repo_slug}'],
                [$username, $repoSlug],
                '/2.0/repositories/{username}/{repo_slug}/pullrequests'
            ),
            [
                'query' => array_replace(
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
                ['{username}', '{repo_slug}', '{pull_request_id}'],
                [$username, $repoSlug, $pullRequestId],
                '/2.0/repositories/{username}/{repo_slug}/pullrequests/{pull_request_id}'
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
