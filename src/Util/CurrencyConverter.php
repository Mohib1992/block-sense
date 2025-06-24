<?php
namespace BlockSense\Util;

interface ExchangeRateProviderInterface {
    public function getRate(string $fromCurrency, string $toCurrency): float;
}

class CurrencyConverter {
    private $exchangeRateProvider;
    
    public function __construct(ExchangeRateProviderInterface $provider) {
        $this->exchangeRateProvider = $provider;
    }
    
    public function convert(float $amount, string $fromCurrency, string $toCurrency): ?float {
        try {
            $rate = $this->exchangeRateProvider->getRate($fromCurrency, $toCurrency);
            return $amount * $rate;
        } catch (\Exception $e) {
            error_log("Currency conversion failed: " . $e->getMessage());
            return null;
        }
    }
    
    // Aliases for common conversion types
    public function cryptoToFiat(float $amount, string $crypto, string $fiat): ?float {
        return $this->convert($amount, strtoupper($crypto), strtoupper($fiat));
    }
    
    public function fiatToCrypto(float $amount, string $fiat, string $crypto): ?float {
        return $this->convert($amount, strtoupper($fiat), strtoupper($crypto));
    }
    
    public function cryptoToCrypto(float $amount, string $fromCrypto, string $toCrypto): ?float {
        return $this->convert($amount, strtoupper($fromCrypto), strtoupper($toCrypto));
    }
}