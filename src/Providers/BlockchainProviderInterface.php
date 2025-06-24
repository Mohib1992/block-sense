<?php
namespace BlockSense\Providers;

interface BlockchainProviderInterface {
    public function getConfirmations(string $txHash, string $network): int;
    public function calculateRiskScore(string $address): float;
}