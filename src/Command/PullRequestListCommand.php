<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Martiis\BitbucketCli\Command\Traits\ClientAwareTrait;
use Martiis\BitbucketCli\Command\Traits\CommentFormatterTrait;
use Martiis\BitbucketCli\Command\Traits\PageAwareCommandTrait;
use Martiis\BitbucketCli\Command\Traits\QueryAwareCommandTrait;
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
    use ClientAwareTrait,
        PageAwareCommandTrait,
        QueryAwareCommandTrait,
        CommentFormatterTrait;

    protected const ARGUMENT_USERNAME = 'username';
    protected const ARGUMENT_REPO_SLUG = 'repo_slug';
    protected const OPTION_WITH_LINKS = 'with-links';

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

        $this
            ->configurePageOption($this)
            ->configureQueryOption($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->requestPullRequestList(
            array_merge($input->getArguments(), $input->getOptions()),
            $output->getVerbosity()
        );
        [$headers, $rows] = $this->extractTableFromResponse($input, $response);

        $io = new SymfonyStyle($input, $output);
        $io->title('Pull requests');
        $io->table($headers, $rows);
        $io->comment($this->formatComment($response));
    }

    /**
     * @param array $parameters
     * @param int   $verbosity
     *
     * @return array
     */
    protected function requestPullRequestList(array $parameters, int $verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        $response =  $this->requestGetJson(
            str_replace(
                ['{username}', '{repo_slug}'],
                [
                    $parameters[self::ARGUMENT_USERNAME],
                    $parameters[self::ARGUMENT_REPO_SLUG]
                ],
                '/2.0/repositories/{username}/{repo_slug}/pullrequests'
            ),
            [
                'query' => [
                    'q' => $parameters['query'],
                    'page' => $parameters['page'] ?? 1,
                ],
            ]
        );

        if ($verbosity === OutputInterface::VERBOSITY_VERBOSE) {
            dump($response);
        }

        return $response;
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
