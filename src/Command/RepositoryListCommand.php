<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

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

class RepositoryListCommand extends Command
{
    use ClientAwareTrait,
        CommentFormatterTrait,
        PageAwareCommandTrait,
        QueryAwareCommandTrait,
        RoleAwareCommandTrait;

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
        $response = json_decode($this->client->get(
            str_replace(
                '{username}',
                $input->getArgument('username'),
                '/2.0/repositories/{username}'
            ),
            ['query' => [
                'page' => (int) $input->getOption('page'),
                'role' => (string) $input->getOption('role'),
                'q' => (string) $input->getOption('query'),
            ]]
        )->getBody()->getContents(), true);

        $tableRows = [];
        foreach ($response['values'] as $repository) {
            $tableRows[] = [
                $repository['uuid'],
                $repository['name'],
                $repository['slug'],
                $repository['type'],
            ];
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('Repository list');
        $io->table(['Uuid', 'Name', 'Slug', 'Type'], $tableRows);
        $io->comment($this->formatComment($response));
    }
}
