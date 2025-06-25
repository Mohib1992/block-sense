<?php
namespace BlockSense\Tests;

use BlockSense\Rules\UnknownSenderRule;


/**
 * @covers \BlockSense\Rules\UnknownSenderRule
 */
class UnknownSenderRuleTest extends \PHPUnit\Framework\TestCase
{
    public function testTransactionFromKnownSenderIsNotFlagged()
    {
        $rule = new UnknownSenderRule(['0xKNOWN1', '0xKNOWN2']);
        $tx = ['from' => '0xKNOWN1', 'to' => '0xANY'];
        $this->assertFalse(($rule->apply())($tx));
    }

    public function testTransactionFromUnknownSenderIsFlagged()
    {
        $rule = new UnknownSenderRule(['0xKNOWN1', '0xKNOWN2']);
        $tx = ['from' => '0xUNKNOWN', 'to' => '0xANY'];
        $this->assertTrue(($rule->apply())($tx));
    }

    public function testTransactionWithEmptyKnownWalletsAlwaysFlags()
    {
        $rule = new UnknownSenderRule([]);
        $tx = ['from' => '0xANY', 'to' => '0xANY2'];
        $this->assertTrue(($rule->apply())($tx));
    }

    public function testTransactionWithCaseSensitiveAddresses()
    {
        $rule = new UnknownSenderRule(['0xKNOWN']);
        $tx = ['from' => '0xknown', 'to' => '0xANY'];
        $this->assertTrue(($rule->apply())($tx));
    }

    public function testTransactionWithMultipleKnownWallets()
    {
        $rule = new UnknownSenderRule(['0xA', '0xB', '0xC']);
        $tx = ['from' => '0xB', 'to' => '0xD'];
        $this->assertFalse(($rule->apply())($tx));
        $tx2 = ['from' => '0xD', 'to' => '0xA'];
        $this->assertTrue(($rule->apply())($tx2));
    }
}
