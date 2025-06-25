<?php

namespace BlockSense\Rules;

use BlockSense\Providers\FraudRuleInterface;
use PHPUnit\Framework\TestCase;

class HighValueRule implements FraudRuleInterface
{
    private float $threshold;

    public function __construct(float $threshold)
    {
        $this->threshold = $threshold;
    }

    /**
     * Creates a rule to flag high-value transactions
     *
     * @param float $threshold The minimum value (in base currency units) to flag
     * @return callable A rule function that returns true for transactions above threshold
     */
    public function apply(): callable
    {
        return fn ($tx) => $tx['value'] > $this->threshold;
    }
}
