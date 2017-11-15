<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Client;

interface BitbucketClientInterface
{
    /**
     * @param string $role
     * @param array  $options
     *
     * @return array
     */
    public function getTeamList(string $role, array $options = []);

    /**
     * @param string $owner
     * @param array  $options
     *
     * @return array
     */
    public function getTeamProjectList(string $owner, array $options = []);

    /**
     * @param string $username
     * @param array  $options
     *
     * @return array
     */
    public function getRepositoryList(string $username, array $options = []);

    /**
     * @param string $username
     * @param string $repoSlug
     * @param array  $options
     *
     * @return array
     */
    public function getPullRequestList(string $username, string $repoSlug, array $options = []);

    /**
     * @param string $username
     * @param string $repoSlug
     * @param int    $pullRequestId
     * @param array  $options
     *
     * @return array
     */
    public function getPullRequest(string $username, string $repoSlug, int $pullRequestId, array $options = []);
}
