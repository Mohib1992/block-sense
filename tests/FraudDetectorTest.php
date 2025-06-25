<?php
namespace BlockSense\Tests;

use PHPUnit\Framework\TestCase;

class FraudDetectorTest extends TestCase
{
    public function testAddRuleAndCheckTransaction()
    {
        $detector = new \BlockSense\FraudDetector();

        // Rule: flag if value > 1000
        $detector->addRule(\BlockSense\FraudDetector::highValueRule(1000));

        $tx1 = ['value' => 500, 'from' => '0xabc'];
        $tx2 = ['value' => 1500, 'from' => '0xabc'];

        $this->assertFalse($detector->checkTransaction($tx1));
        $this->assertTrue($detector->checkTransaction($tx2));
    }

    public function testUnknownSenderRule()
    {
        $detector = new \BlockSense\FraudDetector();

        $knownWallets = ['0xabc', '0xdef'];
        $detector->addRule(\BlockSense\FraudDetector::unknownSenderRule($knownWallets));

        $txKnown = ['value' => 100, 'from' => '0xabc'];
        $txUnknown = ['value' => 100, 'from' => '0x123'];

        $this->assertFalse($detector->checkTransaction($txKnown));
        $this->assertTrue($detector->checkTransaction($txUnknown));
    }

    public function testMultipleRules()
    {
        $detector = new \BlockSense\FraudDetector();

        $detector->addRule(\BlockSense\FraudDetector::highValueRule(1000));
        $detector->addRule(\BlockSense\FraudDetector::unknownSenderRule(['0xabc']));

        $tx = ['value' => 500, 'from' => '0xunknown'];
        $this->assertTrue($detector->checkTransaction($tx));

        $tx2 = ['value' => 1500, 'from' => '0xabc'];
        $this->assertTrue($detector->checkTransaction($tx2));

        $tx3 = ['value' => 500, 'from' => '0xabc'];
        $this->assertFalse($detector->checkTransaction($tx3));
    }
}