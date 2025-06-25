<?php

namespace BlockSense\Providers;

/**
 * Interface for fraud rule implementations.
 *
 * This interface defines the contract for fraud rule classes that can be applied
 * to detect and prevent fraudulent activities. Implementations should return
 * a callable function that performs the actual fraud detection logic.
 *
 * @package BlockSense\Providers
 */
interface FraudRuleInterface
{
    /**
     * Apply the fraud rule and return a callable function.
     *
     * This method should return a callable function that implements the specific
     * fraud detection logic. The returned callable can be executed to perform
     * fraud checks on data or transactions.
     *
     * @return callable A callable function that performs fraud detection
     */
    public function apply(): callable;
}
