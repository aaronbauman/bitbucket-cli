<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Security\Guzzle;

use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;

/**
 * Class BearerMiddleware
 * @package Martiis\BitbucketCli\Security\Guzzle
 */
class BearerMiddleware
{
    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * BearerMiddleware constructor.
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader(
                'Authorization',
                sprintf('Bearer %s', $this->accessToken->getToken())
            );
            return $handler($request, $options);
        };
    }
}