<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullRequestListCommand extends Command
{
    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pull-request:list')
            ->setDescription('Lists pull-requests');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * @required
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}