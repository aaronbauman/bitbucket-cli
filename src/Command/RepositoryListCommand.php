<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Martiis\BitbucketCli\Client\BitbucketClientInterface;
use Martiis\BitbucketCli\Command\Traits\ClientAwareTrait;
use Martiis\BitbucketCli\Command\Traits\CommentFormatterTrait;
use Martiis\BitbucketCli\Command\Traits\PageAwareCommandTrait;
use Martiis\BitbucketCli\Command\Traits\QueryAwareCommandTrait;
use Martiis\BitbucketCli\Command\Traits\RoleAwareCommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class RepositoryListCommand
 * @package Martiis\BitbucketCli\Command
 */
class RepositoryListCommand extends Command
{
    use CommentFormatterTrait,
        PageAwareCommandTrait,
        QueryAwareCommandTrait,
        RoleAwareCommandTrait;

    /**
     * @var BitbucketClientInterface
     */
    private $bitbucketClient;

    /**
     * RepositoryListCommand constructor.
     * @param BitbucketClientInterface $bitbucketClient
     */
    public function __construct(BitbucketClientInterface $bitbucketClient)
    {
        parent::__construct();

        $this->bitbucketClient = $bitbucketClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('repository:list')
            ->setDescription('List of all repositories owned by the specified account or UUID.')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'This can either be the username '
                .'or the UUID of the user, surrounded by curly-braces, for example: {user UUID}.'
            );

        $this
            ->configurePageOption($this)
            ->configureQueryOption($this)
            ->configureRoleOption($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->bitbucketClient->getRepositoryList($input->getArgument('username'), [
            'page' => (int) $input->getOption('page') ?? 1,
            'role' => (string) $input->getOption('role'),
            'q' => (string) $input->getOption('query'),
        ]);
        [$headers, $rows] = $this->extractTableFromResponse($response);

        $io = new SymfonyStyle($input, $output);
        $io->title('Repository list');
        $io->table($headers, $rows);
        $io->comment($this->formatComment($response));
    }

    /**
     * @param array $response
     *
     * @return array
     */
    private function extractTableFromResponse(array $response)
    {
        $tableHeaders = ['Uuid', 'Name', 'Slug', 'Type'];
        $tableRows = [];

        foreach ($response['values'] as $repository) {
            $tableRows[] = [
                $repository['uuid'],
                $repository['name'],
                $repository['slug'],
                $repository['type'],
            ];
        }

        return [$tableHeaders, $tableRows];
    }
}
