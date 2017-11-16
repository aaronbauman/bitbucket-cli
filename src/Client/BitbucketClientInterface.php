<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Client;

interface BitbucketClientInterface
{
    public const VERSION = '2.0';

    public const URI_TEAM_LIST = '/{version}/teams';
    public const URI_TEAM_PROJECT_LIST = '/{version}/teams/{owner}/projects/';
    public const URI_REPOSITORY_LIST = '/{version}/repositories/{username}';
    public const URI_PULL_REQUEST_LIST = '/{version}/repositories/{username}/{repo_slug}/pullrequests';
    public const URI_PULL_REQUEST = '/{version}/repositories/{username}/{repo_slug}/pullrequests/{pull_request_id}';

    public const VARS_TEAM_LIST = ['{version}'];
    public const VARS_TEAM_PROJECT_LIST = ['{version}', '{owner}'];
    public const VARS_REPOSITORY_LIST = ['{version}', '{username}'];
    public const VARS_PULL_REQUEST_LIST = ['{version}', '{username}', '{repo_slug}'];
    public const VARS_PULL_REQUEST = ['{version}', '{username}', '{repo_slug}', '{pull_request_id}'];

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
