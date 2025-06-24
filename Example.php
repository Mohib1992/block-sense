<?php
require 'vendor/autoload.php';

use BlockSense\BlockMonitor;
use BlockSense\FraudDetector;
use BlockSense\Util\Networking\WebSocketServer;
use BlockSense\Util\Logger;

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