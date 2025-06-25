<?php
/**
 * BlockSense Library - User Guide
 * 
 * This file demonstrates how to use the BlockSense library for blockchain monitoring,
 * fraud detection, currency conversion, and compliance reporting.
 */

use BlockSense\BlockMonitor;
use BlockSense\FraudDetector;
use BlockSense\Util\Logger;
use BlockSense\Util\CurrencyConverter;
use BlockSense\ComplianceEngine;
use BlockSense\Rules\HighValueRule;
use BlockSense\Rules\SuspiciousAddressRule;


/**
 * ============================================================================
 * 1. BASIC SETUP AND INITIALIZATION
 * ============================================================================
 */

// Initialize the main components
$monitor = new BlockMonitor('eth', 'YOUR_API_KEY_HERE');
$detector = new FraudDetector();
$logger = new Logger();

/**
 * ============================================================================
 * 2. FRAUD DETECTION CONFIGURATION
 * ============================================================================
 */

// Transactions > 100 ETH (assuming value is in wei)
$detector->addRule(new HighValueRule(100 * 1e18));

// Known suspicious addresses
$detector->addRule(new SuspiciousAddressRule(['0x123...', '0x456...']));

// Example: Add a custom rule class for rapid transactions if available
// $detector->addRule(new RapidTransactionRule(10, 60)); // Uncomment if implemented

// Add custom fraud detection rules (must implement FraudRuleInterface)
class SelfTransferOrZeroValueRule implements \BlockSense\Providers\FraudRuleInterface {
    public function apply(): callable {
        return function($transaction) {
            // Self-transfer detection
            if ($transaction['from'] === $transaction['to']) {
                return true; // Flag as suspicious
            }
            // Zero-value transaction detection
            if ($transaction['value'] == 0) {
                return true; // Flag as suspicious
            }
            return false; // Not suspicious
        };
    }
}
$detector->addRule(new SelfTransferOrZeroValueRule());

/**
 * ============================================================================
 * 3. ADDRESS MONITORING
 * ============================================================================
 */

// Monitor a specific address for suspicious activity
$addressToMonitor = '0x742d35Cc6634C0532925a3b844Bc454e4438f44e';

// Continuous monitoring loop
while (true) {
    try {
        $transactions = $monitor->monitorAddress($addressToMonitor);
        
        foreach ($transactions as $transaction) {
            // Check if transaction is suspicious
            if ($detector->checkTransaction($transaction)) {
                // Log the suspicious transaction
                Logger::logFraud($transaction);
                
                // You can add your custom alert logic here
                echo "Suspicious transaction detected: " . $transaction['hash'] . "\n";
            }
        }
        
        // Wait before next check (15 seconds)
        sleep(15);
        
    } catch (Exception $e) {
        Logger::logError('Monitoring error: ' . $e->getMessage());
        sleep(30); // Wait longer on error
    }
}

/**
 * ============================================================================
 * 4. CURRENCY CONVERSION
 * ============================================================================
 */

// Initialize currency converter with default provider
$converter = new CurrencyConverter();

// Convert crypto to fiat
try {
    $ethAmount = 1.5;
    $usdValue = $converter->cryptoToFiat($ethAmount, 'ETH', 'USD');
    echo "{$ethAmount} ETH = \${$usdValue} USD\n";
} catch (Exception $e) {
    Logger::logError('Currency conversion error: ' . $e->getMessage());
}

// Convert fiat to crypto
try {
    $usdAmount = 100;
    $btcAmount = $converter->fiatToCrypto($usdAmount, 'USD', 'BTC');
    echo "\${$usdAmount} USD = {$btcAmount} BTC\n";
} catch (Exception $e) {
    Logger::logError('Currency conversion error: ' . $e->getMessage());
}

/**
 * ============================================================================
 * 5. COMPLIANCE ENGINE
 * ============================================================================
 */

// Initialize compliance engine
$complianceEngine = new ComplianceEngine();

// Wait for transaction confirmations
$transactionHash = '0x1234567890abcdef...';
try {
    $confirmations = $complianceEngine->waitForConfirmations($transactionHash, 6, 'ETH');
    echo "Transaction confirmed with {$confirmations} confirmations\n";
} catch (Exception $e) {
    Logger::logError('Confirmation error: ' . $e->getMessage());
}

// Generate compliance reports
$addressForReport = '0xabc123def456...';
try {
    // Generate FATF compliance report
    $fatfReport = $complianceEngine->generateReport($addressForReport, ['FATF']);
    echo "FATF Report: " . json_encode($fatfReport, JSON_PRETTY_PRINT) . "\n";
    
    // Generate OFAC compliance report
    $ofacReport = $complianceEngine->generateReport($addressForReport, ['OFAC']);
    echo "OFAC Report: " . json_encode($ofacReport, JSON_PRETTY_PRINT) . "\n";
    
    // Generate comprehensive report
    $comprehensiveReport = $complianceEngine->generateReport($addressForReport, ['FATF', 'OFAC', 'GDPR']);
    echo "Comprehensive Report: " . json_encode($comprehensiveReport, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    Logger::logError('Report generation error: ' . $e->getMessage());
}

/**
 * ============================================================================
 * 6. ADVANCED USAGE EXAMPLES
 * ============================================================================
 */

// Example: Monitor multiple addresses
$addressesToMonitor = [
    '0x742d35Cc6634C0532925a3b844Bc454e4438f44e',
    '0x1234567890abcdef1234567890abcdef12345678',
    '0xabcdef1234567890abcdef1234567890abcdef12'
];

foreach ($addressesToMonitor as $address) {
    $transactions = $monitor->monitorAddress($address);
    // Process transactions...
}

// Example: Custom logging
Logger::logInfo('Application started');
Logger::logWarning('High transaction volume detected');
Logger::logError('API rate limit exceeded');

// Example: Batch processing
$batchSize = 100;
$transactions = $monitor->getBatchTransactions($addressToMonitor, $batchSize);

foreach ($transactions as $transaction) {
    if ($detector->checkTransaction($transaction)) {
        Logger::logFraud($transaction);
    }
}

/**
 * ============================================================================
 * 7. ERROR HANDLING BEST PRACTICES
 * ============================================================================
 */

// Always wrap API calls in try-catch blocks
try {
    $result = $monitor->monitorAddress($addressToMonitor);
} catch (BlockSense\Exceptions\ApiException $e) {
    Logger::logError('API Error: ' . $e->getMessage());
    // Handle API-specific errors
} catch (BlockSense\Exceptions\RateLimitException $e) {
    Logger::logError('Rate limit exceeded: ' . $e->getMessage());
    sleep(60); // Wait before retrying
} catch (Exception $e) {
    Logger::logError('Unexpected error: ' . $e->getMessage());
}

/**
 * ============================================================================
 * 8. CONFIGURATION OPTIONS
 * ============================================================================
 */

// Configure monitoring intervals
$monitor->setPollingInterval(30); // Check every 30 seconds

// Configure fraud detection sensitivity
$detector->setSensitivity(FraudDetector::SENSITIVITY_HIGH);

// Configure currency conversion cache
$converter->setCacheEnabled(true);
$converter->setCacheDuration(300); // 5 minutes

// Configure compliance engine
$complianceEngine->setMaxConfirmations(12);
$complianceEngine->setConfirmationTimeout(3600); // 1 hour

/**
 * ============================================================================
 * 9. PERFORMANCE TIPS
 * ============================================================================
 */

// Use batch processing for large datasets
// Implement proper error handling and retry logic
// Cache frequently accessed data
// Use appropriate polling intervals
// Monitor memory usage with large transaction sets

echo "BlockSense Library User Guide - All examples completed successfully!\n";