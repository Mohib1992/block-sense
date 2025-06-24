<?php
require 'vendor/autoload.php';

use BlockSense\BlockMonitor;
use BlockSense\FraudDetector;
use BlockSense\Util\Networking\WebSocketServer;
use BlockSense\Util\Logger;
use BlockSense\Util\CurrencyConverter;
use BlockSense\ComplianceEngine;

// Initialize
$monitor = new BlockMonitor('eth', 'API_KEY');
$detector = new FraudDetector();
$ws = new WebSocketServer();

// Add fraud rules
$detector->addRule(FraudDetector::highValueRule(100 * 1e18)); // >100 ETH
$detector->addRule(function($tx) {
    return $tx['from'] == $tx['to']; // Self-transfer
});

// Start WebSocket
$ws->start(8080);

// Monitor
while (true) {
    $txs = $monitor->monitorAddress('0x742d35Cc6634C0532925a3b844Bc454e4438f44e');
    
    foreach ($txs as $tx) {
        if ($detector->checkTransaction($tx)) {
            Logger::logFraud($tx);
            $ws->broadcast(['type' => 'fraud', 'data' => $tx]);
        }
    }
    
    sleep(15);
}


### Example Ueses

// Using CoinGecko
$geckoProvider = new CoinGeckoProvider('YOUR_API_KEY');
$converter = new CurrencyConverter($geckoProvider);
$amountInUSD = $converter->cryptoToFiat(1.5, 'ETH', 'USD');

// Using Binance
$binanceProvider = new BinanceProvider();
$converter = new CurrencyConverter($binanceProvider);
$btcAmount = $converter->fiatToCrypto(100, 'USD', 'BTC');

// Custom provider implementation
class MyCustomProvider implements ExchangeRateProviderInterface {
    // ... implementation ...
}
$customConverter = new CurrencyConverter(new MyCustomProvider());

## Uses of Compliance Engine

$provider = new MyBlockchainProvider();
$engine = new ComplianceEngine($provider);

// Wait for 6 confirmations
$engine->waitForConfirmations('0x123...', 6, 'ETH');

// Generate comprehensive report
$report = $engine->generateReport('0xabc...', ['FATF', 'OFAC']);

// Add custom report generator
$engine->addReportGenerator('GDPR', new GdprReportGenerator());