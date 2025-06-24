<?php
namespace BlockSense;

use BlockSense\Util\Networking\HttpClient;
use BlockSense\Util\Networking\WebSocketServer;

class BlockMonitor {
    private $apiKey;
    private $network;
    private $httpClient;
    private $webSocketServer;
    private $webhookUrl;
    private $requiredConfirmations = 3;
    private $lastCheckedBlock = 0;
    
    private $explorerEndpoints = [
        'btc' => 'https://blockchain.info/',
        'eth' => 'https://api.etherscan.io/',
        'bsc' => 'https://api.bscscan.com/'
    ];

    public function __construct(
        string $network, 
        string $apiKey = '', 
        string $webhookUrl = '',
        WebSocketServer $webSocketServer = null
    ) {
        $this->network = strtolower($network);
        if (!isset($this->explorerEndpoints[$this->network])) {
            throw new \InvalidArgumentException("Unsupported network: {$network}");
        }
        
        $this->apiKey = $apiKey;
        $this->webhookUrl = $webhookUrl;
        $this->httpClient = new HttpClient();
        $this->webSocketServer = $webSocketServer;
    }

    public function monitorAddress(string $address, callable $callback = null): array {
        $url = $this->explorerEndpoints[$this->network] . 'api?module=account&action=txlist&address=' . urlencode($address);
        if ($this->apiKey) {
            $url .= '&apikey=' . urlencode($this->apiKey);
        }

        $response = $this->httpClient->get($url);
        $transactions = $response['result'] ?? [];
        
        $newTransactions = [];
        foreach ($transactions as $tx) {
            if ($tx['blockNumber'] > $this->lastCheckedBlock) {
                $newTransactions[] = $tx;
                
                if ($callback) {
                    $callback($tx);
                }
                
                if ($this->webSocketServer) {
                    $this->webSocketServer->broadcast(json_encode([
                        'type' => 'transaction',
                        'data' => $tx
                    ]));
                }
                
                if ($this->webhookUrl) {
                    $this->httpClient->post($this->webhookUrl, [
                        'event' => 'new_transaction',
                        'network' => $this->network,
                        'transaction' => $tx
                    ]);
                }
            }
        }

        if (!empty($transactions)) {
            $this->lastCheckedBlock = max(array_column($transactions, 'blockNumber'));
        }
        
        return $newTransactions;
    }
}