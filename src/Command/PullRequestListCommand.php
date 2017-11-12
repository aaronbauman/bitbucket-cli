<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Martiis\BitbucketCli\Command\Traits\ClientAwareTrait;
use Martiis\BitbucketCli\Command\Traits\CommentFormatterTrait;
use Martiis\BitbucketCli\Command\Traits\PageAwareCommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class PullRequestListCommand
 * @package Martiis\BitbucketCli\Command
 */
class PullRequestListCommand extends Command
{
    use ClientAwareTrait, PageAwareCommandTrait, CommentFormatterTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pull-request:list')
            ->setDescription('List of all pull requests on the specified repository.')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'This can either be the username or the UUID of the user, '
                . 'surrounded by curly-braces, for example: {user UUID}.'
            )
            ->addArgument(
                'repo_slug',
                InputArgument::REQUIRED,
                '	This can either be the repository slug or the UUID of the '
                . 'repository, surrounded by curly-braces, for example: {repository UUID}.'
            )
            ->addOption(
                'with-links',
                'l',
                InputOption::VALUE_NONE,
                'Includes link table'
            );

        $this->configurePageOption($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = json_decode($this->client->get(
            str_replace(
                ['{username}', '{repo_slug}'],
                [$input->getArgument('username'), $input->getArgument('repo_slug')],
                '/2.0/repositories/{username}/{repo_slug}/pullrequests'
            ),
            ['query' => [
                'page' => (int) $input->getOption('page'),
            ]]
        )->getBody()->getContents(), true);

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            dump($response);
        }

        $tableHeaders = ['Id', 'Title', 'Author', 'State'];
        $tableRows = [];

        if ($input->getOption('with-links')) {
            $tableHeaders[] = 'Link';
        }

        foreach ($response['values'] as $pullRequest) {
            $row = [
                $pullRequest['id'],
                $pullRequest['title'],
                $pullRequest['author']['display_name'] ?? $pullRequest['author']['username'],
                $pullRequest['state'],
            ];

            if ($input->getOption('with-links')) {
                $row[] = $pullRequest['links']['html']['href'];
            }

            $tableRows[] = $row;
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Pull requests');
        $io->table($tableHeaders, $tableRows);
        $io->comment($this->formatComment($response));
    }
}
