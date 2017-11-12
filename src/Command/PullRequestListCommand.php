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

    private const ARGUMENT_USERNAME = 'username';
    private const ARGUMENT_REPO_SLUG = 'repo_slug';
    private const OPTION_WITH_LINKS = 'with-links';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pull-request:list')
            ->setDescription('List of all pull requests on the specified repository.')
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
            ->addOption(
                self::OPTION_WITH_LINKS,
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
                [
                    $input->getArgument(self::ARGUMENT_USERNAME),
                    $input->getArgument(self::ARGUMENT_REPO_SLUG)
                ],
                '/2.0/repositories/{username}/{repo_slug}/pullrequests'
            ),
            ['query' => [
                'page' => (int) $input->getOption('page'),
            ]]
        )->getBody()->getContents(), true);

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            dump($response);
        }

        [$headers, $rows] = $this->extractTableFromResponse($input, $response);
        $io = new SymfonyStyle($input, $output);
        $io->title('Pull requests');
        $io->table($headers, $rows);
        $io->comment($this->formatComment($response));
    }

    /**
     * @param InputInterface $input
     * @param array          $response
     *
     * @return array
     */
    private function extractTableFromResponse(InputInterface $input, array $response)
    {
        $tableHeaders = ['Id', 'Title', 'Author', 'State'];
        $tableRows = [];

        if ($input->getOption(self::OPTION_WITH_LINKS)) {
            $tableHeaders[] = 'Link';
        }

        foreach ($response['values'] as $pullRequest) {
            $row = [
                $pullRequest['id'],
                $pullRequest['title'],
                $pullRequest['author']['display_name'] ?? $pullRequest['author']['username'],
                $pullRequest['state'],
            ];

            if ($input->getOption(self::OPTION_WITH_LINKS)) {
                $row[] = $pullRequest['links']['html']['href'];
            }

            $tableRows[] = $row;
        }

        return [$tableHeaders, $tableRows];
    }
}
