<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Martiis\BitbucketCli\Client\BitbucketClientInterface;
use Martiis\BitbucketCli\Command\Traits\CommentFormatterTrait;
use Martiis\BitbucketCli\Command\Traits\PageAwareCommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class TeamProjectListCommand
 * @package Martiis\BitbucketCli\Command
 */
class TeamProjectListCommand extends Command
{
    use CommentFormatterTrait, PageAwareCommandTrait;

    /**
     * @var BitbucketClientInterface
     */
    private $bitbucketClient;

    /**
     * TeamProjectListCommand constructor.
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
            ->setName('team:project:list')
            ->setDescription('List of projects that belong to the specified team.')
            ->addArgument(
                'owner',
                InputArgument::REQUIRED,
                'The team which owns the project. This can either be the username of the team or the '
                . 'UUID of the team (surrounded by curly-braces ({})).'
            );

        $this->configurePageOption($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->bitbucketClient->getTeamProjectList($input->getArgument('owner'), [
            'page' => (int) $input->getOption('page') ?? 1,
        ]);

        $tableRows = [];
        foreach ($response['values'] as $project) {
            $tableRows[] = [
                $project['uuid'],
                $project['name'],
                $project['type'],
            ];
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Team project list');
        $io->table(['Uuid', 'Name', 'Type'], $tableRows);
        $io->comment($this->formatComment($response));
    }
}
