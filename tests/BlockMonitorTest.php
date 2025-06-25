<?php
namespace BlockSense\Tests;

use PHPUnit\Framework\TestCase;
use BlockSense\BlockMonitor;
use BlockSense\Util\Networking\HttpClient;

class BlockMonitorTest extends TestCase {
    
    public function testMonitorAddressReturnsTransactions()
    {
        // Mock HttpClient
        $mockHttpClient = $this->getMockBuilder(HttpClient::class)
            ->onlyMethods(['get'])
            ->getMock();

        $mockHttpClient->method('get')->willReturn([
            'status' => HttpClient::$HTTP_OK,
            'result' => [
                [
                    'blockNumber' => 101,
                    'hash' => '0xabc123',
                    'from' => '0xfrom',
                    'to' => '0xto',
                    'value' => '1000'
                ]
            ]
        ]);

        $monitor = new BlockMonitor('eth', 'dummyApiKey', '', null, $mockHttpClient);

        $txs = $monitor->monitorAddress('0x123...');
        $this->assertIsArray($txs);
        $this->assertCount(1, $txs);
        $this->assertEquals('0xabc123', $txs[0]['hash']);
    }

    public function testMonitorAddressWithNoTransactions()
    {
        $mockHttpClient = $this->getMockBuilder(HttpClient::class)
            ->onlyMethods(['get'])
            ->getMock();

        $mockHttpClient->method('get')->willReturn([
            'status' => HttpClient::$HTTP_OK,
            'result' => []
        ]);

        $monitor = new BlockMonitor('eth', 'dummyApiKey', '', null, $mockHttpClient);

        $txs = $monitor->monitorAddress('0x123...');
        $this->assertIsArray($txs);
        $this->assertCount(0, $txs);
    }

    public function testUnsupportedNetworkThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new BlockMonitor('doge');
    }

    public function testMonitorAddressCallbackIsCalled()
    {
        $mockHttpClient = $this->getMockBuilder(HttpClient::class)
            ->onlyMethods(['get'])
            ->getMock();

        $mockHttpClient->method('get')->willReturn([
            'status' => HttpClient::$HTTP_OK,
            'result' => [
                [
                    'blockNumber' => 101,
                    'hash' => '0xabc123',
                    'from' => '0xfrom',
                    'to' => '0xto',
                    'value' => '1000'
                ]
            ]
        ]);

        $monitor = new BlockMonitor('eth', 'dummyApiKey', '', null, $mockHttpClient);

        $called = false;
        $callback = function ($tx) use (&$called) {
            $called = true;
            $this->assertEquals('0xabc123', $tx['hash']);
        };

        $monitor->monitorAddress('0x123...', $callback);
        $this->assertTrue($called);
    }

    public function testMonitorAddressBroadcastsTransaction()
    {
        $mockHttpClient = $this->getMockBuilder(\BlockSense\Util\Networking\HttpClient::class)
            ->onlyMethods(['get'])
            ->getMock();

        $mockHttpClient->method('get')->willReturn([
            'status' => \BlockSense\Util\Networking\HttpClient::$HTTP_OK,
            'result' => [
                [
                    'blockNumber' => 101,
                    'hash' => '0xabc123',
                    'from' => '0xfrom',
                    'to' => '0xto',
                    'value' => '1000'
                ]
            ]
        ]);

        $mockWebSocket = $this->getMockBuilder(\BlockSense\Providers\WebSocketServerInterface::class)
            ->onlyMethods(['broadcast'])
            ->getMock();

        $mockWebSocket->expects($this->once())
            ->method('broadcast')
            ->with($this->callback(function($payload) {
                $data = json_decode($payload, true);
                return $data['type'] === 'transaction'
                    && $data['data']['hash'] === '0xabc123';
            }));

        $monitor = new \BlockSense\BlockMonitor('eth', 'dummyApiKey', '', $mockWebSocket, $mockHttpClient);

        $monitor->monitorAddress('0x123...');
    }
}