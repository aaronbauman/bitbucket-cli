<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class PullRequestDeclineCommand
 * @package Martiis\BitbucketCli\Command
 */
class PullRequestDeclineCommand extends PullRequestMergeCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('pull-request:decline')
            ->setDescription('Merges pull-request by identifier');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->requestPullRequest($input->getArguments(), $output->getVerbosity());
        $io = new SymfonyStyle($input, $output);
        $io->comment(sprintf('Declining "<comment>%s</comment>" ..', $response['title']));
        $this->client->post($response['links']['decline']['href']);
        $io->comment('<info>Done</info>');
    }
}
