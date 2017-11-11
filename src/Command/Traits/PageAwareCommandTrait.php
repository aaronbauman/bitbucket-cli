<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command\Traits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait PageAwareCommandTrait
 * @package Martiis\BitbucketCli\Command\Traits
 */
trait PageAwareCommandTrait
{
    /**
     * Configures page option.
     *
     * @param Command $command
     *
     * @return $this
     */
    public function configurePageOption(Command $command)
    {
        $command
            ->addOption(
                'page',
                'p',
                InputOption::VALUE_REQUIRED,
                'Page number',
                1
            );

        return $this;
    }
}