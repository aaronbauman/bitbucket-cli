<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command\Traits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait QueryAwareCommandTrait
 * @package Martiis\BitbucketCli\Command\Traits
 */
trait QueryAwareCommandTrait
{
    /**
     * @param Command $command
     *
     * @return $this
     */
    public function configureQueryOption(Command $command)
    {
        $command
            ->addOption(
                'query',
                null,
                InputOption::VALUE_REQUIRED,
                'Query for filtering results'
            );

        return $this;
    }
}