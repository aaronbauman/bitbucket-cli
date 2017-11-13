<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class PullRequestMergeCommand
 * @package Martiis\BitbucketCli\Command
 */
class PullRequestMergeCommand extends PullRequestListCommand
{
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
        $response = $this->requestPullRequestList(
            array_merge($input->getArguments(), [
                'query' => sprintf('id=%s', $input->getArgument(self::ARGUMENT_PULL_REQUEST_ID)),
            ]),
            $output->getVerbosity()
        );

        $pr = array_shift($response['values']);

        $io = new SymfonyStyle($input, $output);
        $io->comment(sprintf('Merging "<comment>%s</comment>" ..', $pr['title']));
        $this->client->post($pr['links']['merge']['href']);
        $io->comment('<info>Done</info>');
    }
}
