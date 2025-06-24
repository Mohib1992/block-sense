# BlockSense - Real-Time Blockchain Monitoring & Fraud Detection for PHP

[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-8892BF.svg?logo=php)](https://php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Tests](https://github.com/yourusername/blocksense/actions/workflows/tests.yml/badge.svg)](https://github.com/yourusername/blocksense/actions)

<img src="https://user-images.githubusercontent.com/.../blocksense-banner.png" alt="BlockSense Banner" width="800">

> Enterprise-grade blockchain security for PHP developers

## ✨ Features

- **Real-time monitoring** (BTC/ETH/BSC) via WebSocket/API
- **Custom fraud rules** (high-value TXs, unknown senders, etc.)
- **Compliance tools** (FATF/OFAC report generation)
- **Laravel & Symfony integration**
- **AI-powered anomaly detection**

## 🚀 Quick Start

### Installation
```bash
composer require blocksense/blockchain-tracker
```

### Example Ueses
```php
// Using CoinGecko
$geckoProvider = new CoinGeckoProvider('YOUR_API_KEY');
$converter = new CurrencyConverter($geckoProvider);
$amountInUSD = $converter->cryptoToFiat(1.5, 'ETH', 'USD');

// Using Binance
$binanceProvider = new BinanceProvider();
$converter = new CurrencyConverter($binanceProvider);
$btcAmount = $converter->fiatToCrypto(100, 'USD', 'BTC');

// Custom provider implementation
class MyCustomProvider implements ExchangeRateProviderInterface {
    // ... implementation ...
}
$customConverter = new CurrencyConverter(new MyCustomProvider());
```

## Uses of Compliance Engine

```php 
$provider = new MyBlockchainProvider();
$engine = new ComplianceEngine($provider);

// Wait for 6 confirmations
$engine->waitForConfirmations('0x123...', 6, 'ETH');

// Generate comprehensive report
$report = $engine->generateReport('0xabc...', ['FATF', 'OFAC']);

// Add custom report generator
$engine->addReportGenerator('GDPR', new GdprReportGenerator());
```