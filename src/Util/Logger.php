<?php

namespace BlockSense\Util;

/**
 * Logger utility class for handling fraud detection logging.
 *
 * This class provides static methods for logging fraudulent transactions
 * to a dedicated log file for analysis and monitoring purposes.
 *
 * @package BlockSense\Util
 * @since 1.0.0
 */
class Logger
{
    /**
     * Logs a fraudulent transaction to the fraud log file.
     *
     * This method appends transaction data to a JSON-formatted log file
     * for fraud detection and analysis. Each transaction is written as
     * a separate JSON object on a new line.
     *
     * @param array $tx The transaction data to log. Should contain all
     *                  relevant transaction information for fraud analysis.
     *                  Common keys might include: transaction_id, amount,
     *                  timestamp, user_id, ip_address, etc.
     *
     * @return void
     *
     * @throws \Exception If the log file cannot be written to due to
     *                    insufficient permissions or disk space issues.
     *
     * @example
     * ```php
     * $transaction = [
     *     'transaction_id' => 'tx_123456',
     *     'amount' => 1500.00,
     *     'timestamp' => '2024-01-15T10:30:00Z',
     *     'user_id' => 'user_789',
     *     'risk_score' => 0.85
     * ];
     * Logger::logFraud($transaction);
     * ```
     *
     * @see fraud.log The output log file where fraud transactions are stored
     */
    public static function logFraud(array $tx): void
    {
        file_put_contents('fraud.log', json_encode($tx) . PHP_EOL, FILE_APPEND);
    }
}
