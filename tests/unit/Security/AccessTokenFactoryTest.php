<?php

namespace Martiis\BitbucketCli\Test\Unit\Security;

use Codeception\Test\Unit;
use Doctrine\Common\Cache\CacheProvider;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Martiis\BitbucketCli\Security\AccessTokenFactory;

class AccessTokenFactoryTest extends Unit
{
    /**
     * @var \Martiis\BitbucketCli\Test\UnitTester
     */
    protected $tester;

    /**
     * @var GenericProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $genericProvider;

    /**
     * @var CacheProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheProvider;

    /**
     * @Override
     */
    protected function _before()
    {
        $this->genericProvider = $this
            ->getMockBuilder(GenericProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheProvider = $this
            ->getMockBuilder(CacheProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    // tests
    public function testGetAccessTokenFromCache()
    {
        $this
            ->cacheProvider
            ->expects($this->once())
            ->method('contains')
            ->with('access_token')
            ->willReturn(true);

        $this
            ->cacheProvider
            ->expects($this->once())
            ->method('fetch')
            ->with('access_token')
            ->willReturn(json_encode(['access_token' => 'foo', 'expires' => time() + 100,]));

        $factory = new AccessTokenFactory($this->genericProvider, $this->cacheProvider);
        $token = $factory->getAccessToken();
        $this->assertInstanceOf(AccessToken::class, $token);
        $this->assertEquals('foo', $token->getToken());
    }

    public function testGetAccessTokenFromCacheExpired()
    {
        $this
            ->cacheProvider
            ->expects($this->once())
            ->method('contains')
            ->with('access_token')
            ->willReturn(true);

        $this
            ->cacheProvider
            ->expects($this->once())
            ->method('fetch')
            ->with('access_token')
            ->willReturn(json_encode([
                'access_token' => 'foo',
                'expires' => time() - 100,
                'refresh_token' => 'bar',
            ]));

        $this
            ->cacheProvider
            ->expects($this->once())
            ->method('save');

        $newAccessToken = $this
            ->getMockBuilder(AccessToken::class)
            ->disableOriginalConstructor()
            ->getMock();
        $newAccessToken
            ->expects($this->once())
            ->method('getExpires')
            ->willReturn(0);

        $this
            ->genericProvider
            ->expects($this->once())
            ->method('getAccessToken')
            ->with('refresh_token', ['refresh_token' => 'bar'])
            ->willReturn($newAccessToken);

        $factory = new AccessTokenFactory($this->genericProvider, $this->cacheProvider);
        $token = $factory->getAccessToken();
        $this->assertEquals($newAccessToken, $token);
    }

    public function testGetAccessTokenFromProvider()
    {
        $this
            ->cacheProvider
            ->expects($this->once())
            ->method('contains')
            ->with('access_token')
            ->willReturn(false);

        $this
            ->cacheProvider
            ->expects($this->once())
            ->method('save');

        $accessToken = $this
            ->getMockBuilder(AccessToken::class)
            ->disableOriginalConstructor()
            ->getMock();
        $accessToken
            ->expects($this->once())
            ->method('getExpires')
            ->willReturn(0);

        $this
            ->genericProvider
            ->expects($this->once())
            ->method('getAccessToken')
            ->with('client_credentials')
            ->willReturn($accessToken);

        $factory = new AccessTokenFactory($this->genericProvider, $this->cacheProvider);
        $token = $factory->getAccessToken();
        $this->assertEquals($accessToken, $token);
    }
}
