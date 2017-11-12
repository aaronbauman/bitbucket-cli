<?php

namespace Martiis\BitbucketCli\Test\Unit\Security\Guzzle;

use Codeception\Test\Unit;
use League\OAuth2\Client\Token\AccessToken;
use Martiis\BitbucketCli\Security\Guzzle\BearerMiddleware;
use Psr\Http\Message\RequestInterface;

/**
 * {@inheritdoc}
 */
class BearerMiddlewareTest extends Unit
{
    /**
     * @var \Martiis\BitbucketCli\Test\UnitTester
     */
    protected $tester;

    public function testInvoke()
    {
        $accessTokenMock = $this
            ->getMockBuilder(AccessToken::class)
            ->disableOriginalConstructor()
            ->getMock();
        $accessTokenMock
            ->expects($this->once())
            ->method('getToken')
            ->willReturn('foo');

        $handler = function ($request, $options) {
            $this->assertInstanceOf(RequestInterface::class, $request);
            $this->assertTrue(is_array($options));
        };

        $middleware = new BearerMiddleware($accessTokenMock);
        $function = $middleware($handler);

        $requestMock = $this
            ->getMockBuilder(RequestInterface::class)
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('withHeader')
            ->with('Authorization', 'Bearer foo')
            ->willReturnSelf();

        $function($requestMock, []);
    }
}
