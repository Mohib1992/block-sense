<?php
namespace BlockSense;

class FraudDetector {
    private $rules = [];

    public function addRule(callable $rule): void {
        $this->rules[] = $rule;
    }

    public function checkTransaction(array $tx): bool {
        foreach ($this->rules as $rule) {
            if ($rule($tx)) return true;
        }
        return false;
    }

    // Pre-built rules
    public static function highValueRule(float $threshold): callable {
        return fn($tx) => $tx['value'] > $threshold;
    }

    public static function unknownSenderRule(array $knownWallets): callable {
        return fn($tx) => !in_array($tx['from'], $knownWallets);
    }
}