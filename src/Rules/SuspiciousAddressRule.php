<?php

namespace BlockSense\Rules;

use BlockSense\Providers\FraudRuleInterface;

/**
 * SuspiciousAddressRule - A fraud detection rule that flags transactions involving suspicious addresses.
 *
 * This rule checks if a transaction's 'from' or 'to' address matches any address in a predefined
 * list of suspicious addresses. It implements the FraudRuleInterface to provide a callable
 * function that can be used to evaluate transactions for fraud detection.
 *
 * @package BlockSense\Rules
 * @implements FraudRuleInterface
 */
class SuspiciousAddressRule implements FraudRuleInterface
{
    /**
     * @var array List of suspicious addresses to check against
     */
    private array $addresses;

    /**
     * Constructor for SuspiciousAddressRule.
     *
     * @param array $addresses An array of addresses (strings) that are considered suspicious
     */
    public function __construct(array $addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * Applies the suspicious address rule to create a callable function.
     *
     * Returns a callable function that takes a transaction array and returns true
     * if the transaction involves any suspicious address (either as sender or recipient).
     *
     * @return callable A function that evaluates a transaction for suspicious addresses
     *                 The function signature is: function(array $tx): bool
     *
     * @example
     * $rule = new SuspiciousAddressRule(['0x123...', '0x456...']);
     * $evaluator = $rule->apply();
     * $isSuspicious = $evaluator(['from' => '0x123...', 'to' => '0x789...']); // Returns true
     */
    public function apply(): callable
    {
        return function ($tx) {
            return in_array($tx['from'], $this->addresses) || in_array($tx['to'], $this->addresses);
        };
    }
}
