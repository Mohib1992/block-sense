<?php

namespace BlockSense;

/**
 * FraudDetector - A flexible transaction fraud detection system
 *
 * This class provides a rule-based approach to detect potentially fraudulent
 * cryptocurrency transactions. It allows for custom fraud detection rules
 * to be added dynamically and provides some pre-built common rules.
 *
 * @package BlockSense
 * @author BlockSense Team
 * @version 1.0.0
 */
class FraudDetector
{
    /** @var callable[] Array of fraud detection rules */
    private $rules = [];

    /**
     * Add a custom fraud detection rule to the detector
     *
     * Each rule should be a callable that accepts a transaction array
     * and returns true if the transaction is suspicious/fraudulent.
     *
     * @param callable $rule A function that takes a transaction array and returns bool
     * @return void
     *
     * @example
     * $detector = new FraudDetector();
     * $detector->addRule(function($tx) {
     *     return $tx['value'] > 10000; // Flag transactions over 10k
     * });
     */
    public function addRule(callable $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * Check if a transaction is potentially fraudulent
     *
     * Evaluates all registered rules against the given transaction.
     * Returns true if ANY rule indicates the transaction is suspicious.
     *
     * @param array $tx Transaction data array with keys like 'from', 'to', 'value', etc.
     * @return bool True if transaction is flagged as suspicious, false otherwise
     *
     * @example
     * $tx = [
     *     'from' => '0x123...',
     *     'to' => '0x456...',
     *     'value' => 5000,
     *     'timestamp' => 1234567890
     * ];
     * $isSuspicious = $detector->checkTransaction($tx);
     */
    public function checkTransaction(array $tx): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule($tx)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create a rule to flag high-value transactions
     *
     * @param float $threshold The minimum value (in base currency units) to flag
     * @return callable A rule function that returns true for transactions above threshold
     *
     * @example
     * $detector = new FraudDetector();
     * $detector->addRule(FraudDetector::highValueRule(10000));
     * // This will flag any transaction with value > 10000
     */
    public static function highValueRule(float $threshold): callable
    {
        return fn ($tx) => $tx['value'] > $threshold;
    }

    /**
     * Create a rule to flag transactions from unknown senders
     *
     * @param array $knownWallets Array of wallet addresses considered trusted
     * @return callable A rule function that returns true for transactions from unknown senders
     *
     * @example
     * $knownWallets = ['0x123...', '0x456...', '0x789...'];
     * $detector = new FraudDetector();
     * $detector->addRule(FraudDetector::unknownSenderRule($knownWallets));
     * // This will flag any transaction from a sender not in the knownWallets array
     */
    public static function unknownSenderRule(array $knownWallets): callable
    {
        return fn ($tx) => !in_array($tx['from'], $knownWallets);
    }
}
