<?php

namespace BlockSense\Exception;

use Throwable;

/**
 * ComplianceException
 *
 * Exception thrown when compliance-related violations are detected during blockchain operations.
 * This exception is used to handle various compliance scenarios such as insufficient transaction
 * confirmations, sanctioned addresses, and unsupported compliance standards.
 *
 * @package BlockSense\Exception
 * @since 1.0.0
 */
class ComplianceException extends \RuntimeException
{
    /** @var array Additional context information about the compliance violation */
    private array $context;

    /**
     * Constructor
     *
     * Creates a new ComplianceException with optional context information.
     *
     * @param string $message The exception message describing the compliance violation
     * @param array $context Additional context data related to the violation
     * @param int $code The exception code (default: 0)
     * @param Throwable|null $previous The previous exception that caused this one
     */
    public function __construct(
        string $message = "",
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the context information associated with this compliance violation
     *
     * Returns additional data that provides more details about the specific
     * compliance violation that occurred.
     *
     * @return array Context data as an associative array
     *
     * @example
     * ```php
     * try {
     *     // Some compliance check
     * } catch (ComplianceException $e) {
     *     $context = $e->getContext();
     *     // $context might contain: ['address' => '0x123...', 'compliance_standard' => 'OFAC']
     * }
     * ```
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create a ComplianceException for insufficient transaction confirmations
     *
     * Used when a transaction doesn't have enough confirmations to meet
     * compliance requirements.
     *
     * @param string $txHash The transaction hash that lacks sufficient confirmations
     * @param int $requiredConfirmations The minimum number of confirmations required
     * @param int $actualConfirmations The actual number of confirmations the transaction has
     * @return self A new ComplianceException instance
     *
     * @example
     * ```php
     * throw ComplianceException::forTransactionConfirmations(
     *     '0x1234567890abcdef...',
     *     6,  // Required confirmations
     *     2   // Actual confirmations
     * );
     * ```
     */
    public static function forTransactionConfirmations(
        string $txHash,
        int $requiredConfirmations,
        int $actualConfirmations
    ): self {
        return new self(
            sprintf(
                'Transaction %s only has %d confirmations (needs %d)',
                $txHash,
                $actualConfirmations,
                $requiredConfirmations
            ),
            [
                'tx_hash' => $txHash,
                'required_confirmations' => $requiredConfirmations,
                'actual_confirmations' => $actualConfirmations
            ]
        );
    }

    /**
     * Create a ComplianceException for a sanctioned address
     *
     * Used when an address appears on a sanctions list (e.g., OFAC) and
     * cannot be processed due to compliance requirements.
     *
     * @param string $address The blockchain address that is sanctioned
     * @return self A new ComplianceException instance with HTTP 423 status code
     *
     * @example
     * ```php
     * throw ComplianceException::forSanctionedAddress('0x1234567890abcdef...');
     * ```
     */
    public static function forSanctionedAddress(string $address): self
    {
        return new self(
            sprintf('Address %s appears on sanctions list', $address),
            [
                'address' => $address,
                'compliance_standard' => 'OFAC'
            ],
            423 // HTTP 423 Locked - For compliance violations
        );
    }

    /**
     * Create a ComplianceException for an unsupported compliance standard
     *
     * Used when a requested compliance standard is not supported by the system.
     *
     * @param string $standard The unsupported compliance standard that was requested
     * @return self A new ComplianceException instance with HTTP 400 status code
     *
     * @example
     * ```php
     * throw ComplianceException::forUnsupportedStandard('UNKNOWN_STANDARD');
     * ```
     */
    public static function forUnsupportedStandard(string $standard): self
    {
        return new self(
            sprintf('Unsupported compliance standard: %s', $standard),
            [
                'requested_standard' => $standard,
                'supported_standards' => ['FATF', 'OFAC', 'GDPR']
            ],
            400 // HTTP 400 Bad Request
        );
    }
}
