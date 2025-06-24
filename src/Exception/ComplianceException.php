<?php
namespace BlockSense\Exception;

use Throwable;

class ComplianceException  extends \RuntimeException
{

    private array $context;

    public function __construct(
        string $message = "",
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

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
