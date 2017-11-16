<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use GuzzleHttp\ClientInterface;
use Martiis\BitbucketCli\Client\BitbucketClientInterface;
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
    protected const ARGUMENT_USERNAME = 'username';
    protected const ARGUMENT_REPO_SLUG = 'repo_slug';
    protected const ARGUMENT_PULL_REQUEST_ID = 'pull-request_id';

    /**
     * @var BitbucketClientInterface
     */
    protected $bitbucketClient;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * PullRequestMergeCommand constructor.
     *
     * @param BitbucketClientInterface $bitbucketClient
     * @param ClientInterface          $httpClient
     */
    public function __construct(BitbucketClientInterface $bitbucketClient, ClientInterface $httpClient)
    {
        parent::__construct();

        $this->bitbucketClient = $bitbucketClient;
        $this->httpClient = $httpClient;
    }

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
        $response = $this->bitbucketClient->getPullRequest(
            $input->getArgument(self::ARGUMENT_USERNAME),
            $input->getArgument(self::ARGUMENT_REPO_SLUG),
            $input->getArgument(self::ARGUMENT_PULL_REQUEST_ID)
        );

        $io = new SymfonyStyle($input, $output);
        $io->comment(sprintf('Merging "<comment>%s</comment>" ..', $response['title']));
        $this->httpClient->request('POST', $response['links']['merge']['href']);
        $io->comment('<info>Done</info>');
    }
}
