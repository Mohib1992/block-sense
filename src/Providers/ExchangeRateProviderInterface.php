<?php

namespace BlockSense\Providers;

/**
 * Interface for exchange rate providers.
 *
 * This interface defines the contract for service providers that supply
 * currency exchange rates, such as fiat-to-fiat, crypto-to-fiat, or crypto-to-crypto.
 *
 * Implementations should handle fetching and returning the latest available
 * exchange rate between two specified currencies. The interface provides a
 * standardized way to retrieve exchange rates regardless of the underlying
 * data source or provider.
 *
 * Key responsibilities:
 * - Fetch real-time or cached exchange rates from external APIs or services
 * - Handle currency pair validation and normalization
 * - Return exchange rates as floating-point numbers
 * - Implement appropriate error handling for failed requests
 * - Support various currency formats (ISO 4217 codes, cryptocurrency symbols)
 *
 * Usage example:
 * ```php
 * $provider = new SomeExchangeRateProvider();
 * $rate = $provider->getRate('USD', 'EUR'); // Returns 0.85
 * ```
 *
 * @package BlockSense\Providers
 * @since 1.0.0
 */

interface ExchangeRateProviderInterface
{
    public function getRate(string $fromCurrency, string $toCurrency): float;
}
