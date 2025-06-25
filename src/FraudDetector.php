<?php

namespace BlockSense;

/**
 * FraudDetector - A flexible transaction fraud detection system
 *
 * This class provides a rule-based approach to detect potentially fraudulent
 * cryptocurrency transactions. It allows for custom fraud detection rules
 * to be added dynamically and provides some pre-built common rules.
 *
 * The detector evaluates transactions against a collection of rules, where
 * each rule implements the FraudRuleInterface. A transaction is flagged as
 * suspicious if ANY rule returns true for the given transaction data.
 *
 * Usage:
 * ```php
 * $detector = new FraudDetector();
 * $detector->addRule(new HighValueRule(1000));
 * $detector->addRule(new UnusualPatternRule());
 *
 * $isSuspicious = $detector->checkTransaction($transactionData);
 * ```
 *
 * @package BlockSense
 * @author BlockSense Team
 * @version 1.0.0
 * @since 1.0.0
 */
class FraudDetector
{
    /** @var FraudRuleInterface[] Array of fraud detection rules */
    private $rules = [];

    /**
     * Add a fraud detection rule to the detector
     *
     * Rules are evaluated in the order they are added. Each rule should
     * implement the FraudRuleInterface and return a callable that takes
     * a transaction array as its parameter.
     *
     * @param FraudRuleInterface $rule An object implementing FraudRuleInterface
     * @return void
     * @throws \InvalidArgumentException If the rule parameter is null or invalid
     */
    public function addRule(\BlockSense\Providers\FraudRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * Check if a transaction is potentially fraudulent
     *
     * Evaluates all registered rules against the given transaction.
     * Returns true if ANY rule indicates the transaction is suspicious.
     * Rules are evaluated in the order they were added, and evaluation
     * stops as soon as the first rule returns true.
     *
     * Expected transaction data format:
     * ```php
     * [
     *     'from' => 'sender_address',
     *     'to' => 'recipient_address',
     *     'value' => 100.50,
     *     'timestamp' => 1234567890,
     *     // ... other transaction properties
     * ]
     * ```
     *
     * @param array $tx Transaction data array with keys like 'from', 'to', 'value', etc.
     * @return bool True if transaction is flagged as suspicious, false otherwise
     * @throws \InvalidArgumentException If transaction data is malformed or missing required fields
     */
    public function checkTransaction(array $tx): bool
    {
        foreach ($this->rules as $rule) {
            $callable = $rule->apply();
            if ($callable($tx)) {
                return true;
            }
        }
        return false;
    }
}
