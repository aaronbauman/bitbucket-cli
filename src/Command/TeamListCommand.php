<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Martiis\BitbucketCli\Client\BitbucketClientInterface;
use Martiis\BitbucketCli\Command\Traits\ClientAwareTrait;
use Martiis\BitbucketCli\Command\Traits\CommentFormatterTrait;
use Martiis\BitbucketCli\Command\Traits\PageAwareCommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class TeamListCommand
 * @package Martiis\BitbucketCli\Command
 */
class TeamListCommand extends Command
{
    use CommentFormatterTrait, PageAwareCommandTrait;

    /**
     * @var BitbucketClientInterface
     */
    private $bitbucketClient;

    /**
     * TeamListCommand constructor.
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
            ->setName('team:list')
            ->setDescription('Returns all the teams that the authenticated user is associated with.')
            ->addArgument(
                'role',
                InputArgument::OPTIONAL,
                'Filters the teams based on the authenticated user\'s role on each team.',
                'member'
            );

        $this->configurePageOption($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->bitbucketClient->getTeamList(
            $input->getArgument('role'),
            ['page' => $input->getOption('page')]
        );

        $tableRows = [];
        foreach ($response['values'] as $team) {
            $tableRows[] = [$team['uuid'], $team['username'], $team['display_name'], $team['type']];
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Team list');
        $io->table(['Uuid', 'Username', 'Display name', 'Type'], $tableRows);

        $io->comment($this->formatComment($response));
    }
}
