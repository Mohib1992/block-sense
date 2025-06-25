<?php

namespace BlockSense\Tests;

use PHPUnit\Framework\TestCase;
use BlockSense\Rules\HighValueRule;

class HighValueRuleTest extends TestCase
{
    public function testTransactionBelowThresholdIsNotFlagged()
    {
        $rule = new HighValueRule(1000);
        $tx = ['value' => 500];
        $this->assertFalse(($rule->apply())($tx));
    }

    public function testTransactionEqualToThresholdIsNotFlagged()
    {
        $rule = new HighValueRule(1000);
        $tx = ['value' => 1000];
        $this->assertFalse(($rule->apply())($tx));
    }

    public function testTransactionAboveThresholdIsFlagged()
    {
        $rule = new HighValueRule(1000);
        $tx = ['value' => 1500];
        $this->assertTrue(($rule->apply())($tx));
    }

    public function testTransactionWithFloatValue()
    {
        $rule = new HighValueRule(1000.50);
        $tx = ['value' => 1000.51];
        $this->assertTrue(($rule->apply())($tx));
        $tx2 = ['value' => 1000.50];
        $this->assertFalse(($rule->apply())($tx2));
    }
}