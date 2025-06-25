<?php

namespace BlockSense\Providers;

/**
 * Interface for report generation providers.
 *
 * This interface defines the contract for classes that generate reports
 * based on blockchain addresses. Implementations should provide specific
 * logic for analyzing and generating reports for different types of
 * blockchain data or services.
 *
 * @package BlockSense\Providers
 * @since 1.0.0
 */
interface ReportGeneratorInterface
{
    /**
     * Generate a report for the specified blockchain address.
     *
     * This method should analyze the given address and return a structured
     * array containing relevant report data. The exact structure and content
     * of the report will depend on the specific implementation.
     *
     * @param string $address The blockchain address to generate a report for.
     *                        This should be a valid address format for the
     *                        blockchain network being analyzed.
     *
     * @return array An associative array containing the generated report data.
     *               The structure should be consistent within each implementation
     *               but may vary between different providers.
     *
     * @throws \InvalidArgumentException If the provided address is invalid or
     *                                   not supported by the implementation.
     * @throws \RuntimeException If there's an error during report generation,
     *                           such as network issues or data unavailability.
     *
     * @example
     * ```php
     * $generator = new SomeReportGenerator();
     * $report = $generator->generate('0x742d35Cc6634C0532925a3b8D4C9db96C4b4d8b6');
     * // Returns: ['balance' => '1.5 ETH', 'transactions' => 42, ...]
     * ```
     */
    public function generate(string $address): array;
}
