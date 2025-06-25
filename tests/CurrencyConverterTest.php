<?php


namespace BlockSense\Tests\Util;

use PHPUnit\Framework\TestCase;
use BlockSense\Util\CurrencyConverter;
use BlockSense\Providers\ExchangeRateProviderInterface;


class CurrencyConverterTest extends TestCase
{
    public function testConvertReturnsCorrectValue()
    {
        $mockProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $mockProvider->method('getRate')
            ->with('USD', 'EUR')
            ->willReturn(0.9);

        $converter = new CurrencyConverter($mockProvider);
        $result = $converter->convert(100, 'USD', 'EUR');
        $this->assertEquals(90, $result);
    }

    public function testConvertHandlesExceptionAndReturnsNull()
    {
        $mockProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $mockProvider->method('getRate')
            ->will($this->throwException(new \Exception("API error")));

        $converter = new CurrencyConverter($mockProvider);
        $result = $converter->convert(100, 'USD', 'EUR');
        $this->assertNull($result);
    }

    public function testCryptoToFiatCallsConvertWithUppercase()
    {
        $mockProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $mockProvider->expects($this->once())
            ->method('getRate')
            ->with('BTC', 'USD')
            ->willReturn(500.00);

        $converter = new CurrencyConverter($mockProvider);
        $result = $converter->cryptoToFiat(0.01, 'btc', 'usd');
        $this->assertEquals(5.0, $result);
    }

    public function testFiatToCryptoCallsConvertWithUppercase()
    {
        $mockProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $mockProvider->expects($this->once())
            ->method('getRate')
            ->with('USD', 'ETH')
            ->willReturn(0.0005);

        $converter = new CurrencyConverter($mockProvider);
        $result = $converter->fiatToCrypto(2000, 'usd', 'eth');
        $this->assertEquals(1, $result);
    }

    public function testCryptoToCryptoCallsConvertWithUppercase()
    {
        $mockProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $mockProvider->expects($this->once())
            ->method('getRate')
            ->with('BTC', 'ETH')
            ->willReturn(15.00);

        $converter = new CurrencyConverter($mockProvider);
        $result = $converter->cryptoToCrypto(2, 'btc', 'eth');
        $this->assertEquals(30, $result);
    }
}