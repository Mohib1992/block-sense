<?php
namespace BlockSense\Tests;

use BlockSense\Rules\SuspiciousAddressRule;

/**
 * @covers \BlockSense\Rules\SuspiciousAddressRule
 */
class SuspiciousAddressRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testTransactionFromSuspiciousAddressIsFlagged()
    {
        $rule = new SuspiciousAddressRule(['0xBAD1', '0xBAD2']);
        $tx = ['from' => '0xBAD1', 'to' => '0xGOOD'];
        $this->assertTrue(($rule->apply())($tx));
    }

    public function testTransactionToSuspiciousAddressIsFlagged()
    {
        $rule = new SuspiciousAddressRule(['0xBAD1', '0xBAD2']);
        $tx = ['from' => '0xGOOD', 'to' => '0xBAD2'];
        $this->assertTrue(($rule->apply())($tx));
    }

    public function testTransactionWithNoSuspiciousAddressesIsNotFlagged()
    {
        $rule = new SuspiciousAddressRule(['0xBAD1', '0xBAD2']);
        $tx = ['from' => '0xGOOD', 'to' => '0xALSO_GOOD'];
        $this->assertFalse(($rule->apply())($tx));
    }

    public function testTransactionWithBothFromAndToSuspiciousIsFlagged()
    {
        $rule = new SuspiciousAddressRule(['0xBAD1', '0xBAD2']);
        $tx = ['from' => '0xBAD1', 'to' => '0xBAD2'];
        $this->assertTrue(($rule->apply())($tx));
    }

    public function testEmptySuspiciousAddressListNeverFlags()
    {
        $rule = new SuspiciousAddressRule([]);
        $tx = ['from' => '0xANY', 'to' => '0xANY2'];
        $this->assertFalse(($rule->apply())($tx));
    }
}