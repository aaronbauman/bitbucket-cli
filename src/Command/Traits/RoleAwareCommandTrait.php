<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command\Traits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait RoleAwareCommandTrait
 * @package Martiis\BitbucketCli\Command\Traits
 */
trait RoleAwareCommandTrait
{
    /**
     * @param Command $command
     *
     * @return $this
     */
    public function configureRoleOption(Command $command)
    {
        $command
            ->addOption(
                'role',
                'r',
                InputOption::VALUE_REQUIRED,
                'Filters the result based on the authenticated user\'s role'
            );

        return $this;
    }
}