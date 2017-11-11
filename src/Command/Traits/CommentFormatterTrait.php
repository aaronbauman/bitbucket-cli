<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Command\Traits;

trait CommentFormatterTrait
{
    /**
     * Formats comment for the end of every command.
     *
     * @param array $response
     *
     * @return string
     */
    public function formatComment(array $response)
    {
        return sprintf(
            '<comment>%s</comment> items per page.'
            . ' Total items <comment>%s</comment>.'
            . ' Current page <comment>%s</comment>.',
            $response['pagelen'],
            $response['size'],
            $response['page']
        );
    }
}
