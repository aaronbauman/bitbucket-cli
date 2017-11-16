<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class PullRequestApproveCommand
 * @package Martiis\BitbucketCli\Command
 */
class PullRequestApproveCommand extends PullRequestMergeCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('pull-request:approve')
            ->setDescription('Approved pull-request by identifier');
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
        $io->comment(sprintf('Approving "<comment>%s</comment>" ..', $response['title']));
        $this->httpClient->request('POST', $response['links']['approve']['href']);
        $io->comment('<info>Done</info>');
    }
}
