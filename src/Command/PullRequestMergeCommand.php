<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Martiis\BitbucketCli\Command\Traits\ClientAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class PullRequestMergeCommand
 * @package Martiis\BitbucketCli\Command
 */
class PullRequestMergeCommand extends Command
{
    use ClientAwareTrait;

    protected const ARGUMENT_USERNAME = 'username';
    protected const ARGUMENT_REPO_SLUG = 'repo_slug';
    protected const ARGUMENT_PULL_REQUEST_ID = 'pull-request_id';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pull-request:merge')
            ->setDescription('Merges pull-request by identifier')
            ->addArgument(
                self::ARGUMENT_USERNAME,
                InputArgument::REQUIRED,
                'This can either be the username or the UUID of the user, '
                . 'surrounded by curly-braces, for example: {user UUID}.'
            )
            ->addArgument(
                self::ARGUMENT_REPO_SLUG,
                InputArgument::REQUIRED,
                '	This can either be the repository slug or the UUID of the '
                . 'repository, surrounded by curly-braces, for example: {repository UUID}.'
            )
            ->addArgument(
                self::ARGUMENT_PULL_REQUEST_ID,
                InputArgument::REQUIRED,
                'Pull request identifier from pull-request:list command.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->requestPullRequest($input->getArguments(), $output->getVerbosity());
        $io = new SymfonyStyle($input, $output);
        $io->comment(sprintf('Merging "<comment>%s</comment>" ..', $response['title']));
        $this->client->post($response['links']['merge']['href']);
        $io->comment('<info>Done</info>');
    }

    /**
     * @param array $parameters
     * @param int   $verbosity
     *
     * @return array
     */
    protected function requestPullRequest(array $parameters, int $verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        $response =  $this->requestGetJson(
            str_replace(
                ['{username}', '{repo_slug}', '{pull_request_id}'],
                [
                    $parameters[self::ARGUMENT_USERNAME],
                    $parameters[self::ARGUMENT_REPO_SLUG],
                    $parameters[self::ARGUMENT_PULL_REQUEST_ID],
                ],
                '/2.0/repositories/{username}/{repo_slug}/pullrequests/{pull_request_id}'
            )
        );

        if ($verbosity === OutputInterface::VERBOSITY_VERBOSE) {
            dump($response);
        }

        return $response;
    }
}
