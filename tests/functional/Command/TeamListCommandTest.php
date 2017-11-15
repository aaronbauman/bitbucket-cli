<?php
namespace Martiis\BitbucketCli\Test\Functional\Command;

use Codeception\Test\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Martiis\BitbucketCli\Command\TeamListCommand;
use Martiis\BitbucketCli\Test\FunctionalTester;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TeamListCommandTest extends Unit
{
    /**
     * @var FunctionalTester
     */
    protected $tester;

    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $guzzle;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->guzzle = $this
            ->getMockBuilder(Client::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    // tests
    public function testExecute()
    {
        $this->markTestSkipped();

        $responseMock = $this
            ->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBody', 'getContents'])
            ->getMock();
        $responseMock->expects($this->once())->method('getBody')->willReturnSelf();
        $responseMock->expects($this->once())->method('getContents')->willReturn(json_encode([
            'values' => [
                [
                    'uuid' => '_uuid',
                    'username' => '_username',
                    'display_name' => '_display_name',
                    'type' => '_type',
                ],
            ],
            'pagelen' => 10,
            'size' => 1,
            'page' => 1,
        ]));

        $this
            ->guzzle
            ->expects($this->once())
            ->method('get')
            ->with('/2.0/teams', ['query' => ['page' => 1, 'role' => 'contributor']])
            ->willReturn($responseMock);

        $command = new TeamListCommand();
        $command->setClient($this->guzzle);

        $app = new Application();
        $app->add($command);
        $command = $app->find('team:list');
        $commandTester = new CommandTester($command);
        $this->assertEquals(0, $commandTester->execute(['role' => 'contributor']));
    }
}
