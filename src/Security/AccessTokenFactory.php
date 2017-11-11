<?php

declare(strict_types=1);

namespace Martiis\BitbucketCli\Security;

use Doctrine\Common\Cache\CacheProvider;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Class AccessTokenFactory
 * @package Martiis\BitbucketCli\DependencyInjection
 */
class AccessTokenFactory
{
    private const CACHE_KEY = 'access_token';

    /**
     * @var GenericProvider
     */
    private $genericProvider;

    /**
     * @var CacheProvider
     */
    private $cacheProvider;

    /**
     * AccessTokenFactory constructor.
     * @param GenericProvider $genericProvider
     * @param CacheProvider $cacheProvider
     */
    public function __construct(GenericProvider $genericProvider, CacheProvider $cacheProvider)
    {
        $this->genericProvider = $genericProvider;
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken()
    {
        if (!$this->cacheProvider->contains(self::CACHE_KEY)) {
            $token = $this->genericProvider->getAccessToken('client_credentials');
            $this->saveAccessToken($token);

            return $token;
        }

        $token = new AccessToken(json_decode($this->cacheProvider->fetch(self::CACHE_KEY), true));
        if ($token->hasExpired()) {
            $token = $this->genericProvider->getAccessToken('refresh_token', [
                'refresh_token' => $token->getRefreshToken()
            ]);

            $this->saveAccessToken($token);
        }

        return $token;
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return bool
     */
    private function saveAccessToken(AccessToken $accessToken)
    {
        return $this->cacheProvider->save(
            self::CACHE_KEY,
            json_encode($accessToken),
            $accessToken->getExpires() - time()
        );
    }
}
