<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Class PullRequestDeclineCommand
 * @package Martiis\BitbucketCli\Command
 */
class PullRequestDeclineCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pull-request:decline')
            ->setDescription('Declines pull-request');
    }
}
