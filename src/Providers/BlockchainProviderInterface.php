<?php

namespace BlockSense\Providers;

/**
 * Interface for blockchain providers that handle various blockchain operations.
 *
 * This interface defines the contract for blockchain service providers that can
 * perform operations such as transaction confirmation checking, risk assessment,
 * address screening, and sanctions list verification.
 *
 * @package BlockSense\Providers
 * @since 1.0.0
 */
interface BlockchainProviderInterface
{
    /**
     * Get the number of confirmations for a specific transaction.
     *
     * This method retrieves the current confirmation count for a transaction
     * on the specified blockchain network. Confirmations indicate how many
     * blocks have been mined since the transaction was included in a block.
     *
     * @param string $txHash The transaction hash to check confirmations for
     * @param string $network The blockchain network (e.g., 'bitcoin', 'ethereum', 'polygon')
     * @return int The number of confirmations for the transaction
     * @throws \InvalidArgumentException If the transaction hash is invalid
     * @throws \RuntimeException If the network is not supported or API is unavailable
     */
    public function getConfirmations(string $txHash, string $network): int;

    /**
     * Calculate a risk score for a blockchain address.
     *
     * This method analyzes a blockchain address and returns a risk score
     * based on various factors such as transaction history, associated
     * addresses, and known risk indicators.
     *
     * @param string $address The blockchain address to analyze
     * @return float Risk score between 0.0 (low risk) and 1.0 (high risk)
     * @throws \InvalidArgumentException If the address format is invalid
     * @throws \RuntimeException If risk calculation service is unavailable
     */
    public function calculateRiskScore(string $address): float;

    /**
     * Retrieve transaction history for a blockchain address.
     *
     * This method fetches the complete transaction history for a given
     * blockchain address, including incoming and outgoing transactions
     * with their details.
     *
     * @param string $address The blockchain address to get history for
     * @return array Array of transaction objects with details like:
     *               - txHash: Transaction hash
     *               - timestamp: Transaction timestamp
     *               - amount: Transaction amount
     *               - type: 'incoming' or 'outgoing'
     *               - confirmations: Number of confirmations
     *               - fee: Transaction fee
     * @throws \InvalidArgumentException If the address format is invalid
     * @throws \RuntimeException If transaction history service is unavailable
     */
    public function getTransactionHistory(string $address): array;

    /**
     * Check if an address is on any sanctions or blacklists.
     *
     * This method screens a blockchain address against various sanctions
     * lists, blacklists, and regulatory databases to identify potential
     * compliance issues.
     *
     * @param string $address The blockchain address to check
     * @return array Array containing sanctions information:
     *               - is_sanctioned: Boolean indicating if address is sanctioned
     *               - lists: Array of list names where address was found
     *               - risk_level: Risk level ('low', 'medium', 'high')
     *               - description: Description of the sanction if applicable
     * @throws \InvalidArgumentException If the address format is invalid
     * @throws \RuntimeException If sanctions checking service is unavailable
     */
    public function checkSanctionsList(string $address): array;

    /**
     * Perform comprehensive address screening.
     *
     * This method performs a comprehensive screening of a blockchain address,
     * combining risk assessment, sanctions checking, and other security
     * measures to provide an overall screening result.
     *
     * @param string $address The blockchain address to screen
     * @return string Screening result status:
     *               - 'clean': Address passed all screenings
     *               - 'suspicious': Address has some risk indicators
     *               - 'blocked': Address is blocked or sanctioned
     *               - 'unknown': Unable to determine status
     * @throws \InvalidArgumentException If the address format is invalid
     * @throws \RuntimeException If screening services are unavailable
     */
    public function screenAddress(string $address): string;
}
