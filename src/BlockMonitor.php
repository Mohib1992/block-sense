<?php

namespace BlockSense;

use BlockSense\Providers\WebSocketServerInterface;
use BlockSense\Util\Networking\HttpClient;

/**
 * BlockMonitor - A comprehensive blockchain monitoring solution
 *
 * This class provides real-time monitoring capabilities for blockchain addresses
 * across multiple networks (Bitcoin, Ethereum, BSC). It supports webhook notifications,
 * WebSocket broadcasting, and custom callback functions for transaction events.
 *
 * Features:
 * - Multi-network support (BTC, ETH, BSC)
 * - Real-time transaction monitoring
 * - Webhook notifications
 * - WebSocket broadcasting
 * - Configurable confirmation requirements
 * - Automatic block tracking
 *
 * @package BlockSense
 * @author BlockSense Team
 * @version 1.0.0
 * @since 1.0.0
 */
class BlockMonitor
{
    /** @var string API key for blockchain explorer services */
    private $apiKey;

    /** @var string The blockchain network to monitor (btc, eth, bsc) */
    private $network;

    /** @var HttpClient HTTP client for API requests */
    private $httpClient;

    /** @var WebSocketServerInterface|null WebSocket server for real-time broadcasting */
    private $webSocketServer;

    /** @var string Webhook URL for transaction notifications */
    private $webhookUrl;

    /** @var int Minimum number of confirmations required for a transaction */
    private $requiredConfirmations = 3;

    /** @var int Last checked block number to avoid duplicate processing */
    private $lastCheckedBlock = 0;

    /** @var array Supported blockchain explorer endpoints */
    private $explorerEndpoints = [
        'btc' => 'https://blockchain.info/',
        'eth' => 'https://api.etherscan.io/',
        'bsc' => 'https://api.bscscan.com/'
    ];

    /**
     * Constructor - Initialize the BlockMonitor with network and configuration
     *
     * Creates a new BlockMonitor instance for monitoring transactions on the specified
     * blockchain network. Supports optional API key for enhanced rate limits and
     * webhook URL for transaction notifications.
     *
     * @param string $network The blockchain network to monitor ('btc', 'eth', 'bsc')
     * @param string $apiKey Optional API key for blockchain explorer services
     * @param string $webhookUrl Optional webhook URL for transaction notifications
     * @param WebSocketServerInterface|null $webSocketServer Optional WebSocket server for real-time broadcasting
     * @param HttpClient|null $httpClient Optional HTTP client instance (auto-created if not provided)
     *
     * @throws \InvalidArgumentException When an unsupported network is specified
     *
     * @example
     * ```php
     * // Basic usage with Bitcoin network
     * $monitor = new BlockMonitor('btc');
     *
     * // Advanced usage with API key and webhook
     * $monitor = new BlockMonitor('eth', 'your-api-key', 'https://your-webhook.com/notify');
     * ```
     */
    public function __construct(
        string $network,
        string $apiKey = '',
        string $webhookUrl = '',
        WebSocketServerInterface $webSocketServer = null,
        HttpClient $httpClient = null
    ) {
        $this->network = strtolower($network);
        if (!isset($this->explorerEndpoints[$this->network])) {
            throw new \InvalidArgumentException("Unsupported network: {$network}");
        }

        $this->apiKey = $apiKey;
        $this->webhookUrl = $webhookUrl;
        $this->httpClient = $httpClient ?: new HttpClient();
        $this->webSocketServer = $webSocketServer;
    }

    /**
     * Monitor a blockchain address for new transactions
     *
     * Fetches and processes transactions for the specified address. Only transactions
     * with block numbers higher than the last checked block are considered new.
     * For each new transaction, the method will:
     * - Execute the optional callback function
     * - Broadcast via WebSocket (if configured)
     * - Send webhook notification (if configured)
     *
     * @param string $address The blockchain address to monitor
     * @param callable|null $callback Optional callback function to execute for each new transaction
     *                                The callback receives the transaction data as parameter
     *
     * @return array Array of new transactions found since last check
     *
     * @example
     * ```php
     * // Basic monitoring
     * $transactions = $monitor->monitorAddress('1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa');
     *
     * // With callback function
     * $transactions = $monitor->monitorAddress('0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6', function($tx) {
     *     echo "New transaction: " . $tx['hash'] . "\n";
     * });
     * ```
     */
    public function monitorAddress(string $address, callable $callback = null): array
    {
        $url = $this->explorerEndpoints[$this->network] . 'api?module=account&action=txlist&address=' . urlencode($address);
        if ($this->apiKey) {
            $url .= '&apikey=' . urlencode($this->apiKey);
        }

        $response = $this->httpClient->get($url);
        $transactions = ($response['status'] === HttpClient::$HTTP_OK) ? $response['result'] : [];

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
