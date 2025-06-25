<?php

namespace BlockSense\Rules;

use BlockSense\Providers\FraudRuleInterface;

/**
 * Fraud rule that identifies transactions from unknown senders.
 *
 * This rule checks if the sender address in a transaction is not in the list
 * of known/whitelisted wallet addresses. Transactions from unknown senders
 * are flagged as potentially fraudulent.
 */
class UnknownSenderRule implements FraudRuleInterface
{
    /** @var array List of known/whitelisted wallet addresses */
    private array $knownWallets;

    /**
     * Constructor for UnknownSenderRule.
     *
     * @param array $knownWallets Array of wallet addresses that are considered known/trusted
     */
    public function __construct(array $knownWallets)
    {
        $this->knownWallets = $knownWallets;
    }

    /**
     * Applies the unknown sender fraud detection rule.
     *
     * Returns a callable function that takes a transaction array and returns true
     * if the sender is unknown (not in the known wallets list), indicating potential fraud.
     *
     * @return callable A function that takes a transaction array and returns boolean
     *                  - true: sender is unknown (potential fraud)
     *                  - false: sender is known (not fraud)
     */
    public function apply(): callable
    {
        return function ($tx) {
            return !in_array($tx['from'], $this->knownWallets);
        };
    }
}
