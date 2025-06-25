<?php
namespace BlockSense\Tests;

use PHPUnit\Framework\TestCase;
use BlockSense\FraudDetector;
use BlockSense\Providers\FraudRuleInterface;

class FraudDetectorTest extends TestCase
{
    public function testAddRuleAndCheckTransaction()
    {
        $detector = new FraudDetector();

        // Create a mock rule that flags transactions with value > 1000
        $mockRule = $this->createMock(FraudRuleInterface::class);
        $mockRule->expects($this->any())
            ->method('apply')
            ->willReturn(function ($tx) {
                return $tx['value'] > 1000;
            });

        $detector->addRule($mockRule);

        $tx1 = ['value' => 500, 'from' => '0xabc'];
        $tx2 = ['value' => 1500, 'from' => '0xabc'];

        $this->assertFalse($detector->checkTransaction($tx1));
        $this->assertTrue($detector->checkTransaction($tx2));
    }
}