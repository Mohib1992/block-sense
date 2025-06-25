<?php

namespace BlockSense\Util;

use BlockSense\Providers\ExchangeRateProviderInterface;

/**
 * Currency Converter Utility Class
 *
 * Provides functionality to convert between different currencies including
 * cryptocurrency and fiat currency conversions. This class acts as a wrapper
 * around an exchange rate provider to perform currency conversions.
 *
 * @package BlockSense\Util
 * @author BlockSense
 * @since 1.0.0
 */
class CurrencyConverter
{
    /**
     * The exchange rate provider instance
     *
     * @var ExchangeRateProviderInterface
     */
    private $exchangeRateProvider;

    /**
     * Constructor
     *
     * @param ExchangeRateProviderInterface $provider The exchange rate provider to use for conversions
     */
    public function __construct(ExchangeRateProviderInterface $provider)
    {
        $this->exchangeRateProvider = $provider;
    }

    /**
     * Convert an amount from one currency to another
     *
     * Performs a currency conversion using the configured exchange rate provider.
     * Returns null if the conversion fails due to an exception.
     *
     * @param float $amount The amount to convert
     * @param string $fromCurrency The source currency code (e.g., 'USD', 'BTC')
     * @param string $toCurrency The target currency code (e.g., 'EUR', 'ETH')
     * @return float|null The converted amount, or null if conversion fails
     *
     * @throws \Exception When the exchange rate provider fails to retrieve rates
     *
     * @example
     * $converter = new CurrencyConverter($provider);
     * $result = $converter->convert(100, 'USD', 'EUR'); // Converts 100 USD to EUR
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): ?float
    {
        try {
            $rate = $this->exchangeRateProvider->getRate($fromCurrency, $toCurrency);
            return $amount * $rate;
        } catch (\Exception $e) {
            error_log("Currency conversion failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert cryptocurrency to fiat currency
     *
     * Convenience method for converting cryptocurrency amounts to fiat currency.
     * Automatically converts currency codes to uppercase.
     *
     * @param float $amount The cryptocurrency amount to convert
     * @param string $crypto The source cryptocurrency code (e.g., 'BTC', 'ETH')
     * @param string $fiat The target fiat currency code (e.g., 'USD', 'EUR')
     * @return float|null The converted fiat amount, or null if conversion fails
     *
     * @example
     * $converter = new CurrencyConverter($provider);
     * $usdAmount = $converter->cryptoToFiat(0.5, 'BTC', 'USD'); // Converts 0.5 BTC to USD
     */
    public function cryptoToFiat(float $amount, string $crypto, string $fiat): ?float
    {
        return $this->convert($amount, strtoupper($crypto), strtoupper($fiat));
    }

    /**
     * Convert fiat currency to cryptocurrency
     *
     * Convenience method for converting fiat currency amounts to cryptocurrency.
     * Automatically converts currency codes to uppercase.
     *
     * @param float $amount The fiat amount to convert
     * @param string $fiat The source fiat currency code (e.g., 'USD', 'EUR')
     * @param string $crypto The target cryptocurrency code (e.g., 'BTC', 'ETH')
     * @return float|null The converted cryptocurrency amount, or null if conversion fails
     *
     * @example
     * $converter = new CurrencyConverter($provider);
     * $btcAmount = $converter->fiatToCrypto(1000, 'USD', 'BTC'); // Converts 1000 USD to BTC
     */
    public function fiatToCrypto(float $amount, string $fiat, string $crypto): ?float
    {
        return $this->convert($amount, strtoupper($fiat), strtoupper($crypto));
    }

    /**
     * Convert between different cryptocurrencies
     *
     * Convenience method for converting between different cryptocurrencies.
     * Automatically converts currency codes to uppercase.
     *
     * @param float $amount The cryptocurrency amount to convert
     * @param string $fromCrypto The source cryptocurrency code (e.g., 'BTC', 'ETH')
     * @param string $toCrypto The target cryptocurrency code (e.g., 'ETH', 'LTC')
     * @return float|null The converted cryptocurrency amount, or null if conversion fails
     *
     * @example
     * $converter = new CurrencyConverter($provider);
     * $ethAmount = $converter->cryptoToCrypto(1, 'BTC', 'ETH'); // Converts 1 BTC to ETH
     */
    public function cryptoToCrypto(float $amount, string $fromCrypto, string $toCrypto): ?float
    {
        return $this->convert($amount, strtoupper($fromCrypto), strtoupper($toCrypto));
    }
}
