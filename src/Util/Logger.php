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

    /**
     * Logs an error message to the error log file.
     *
     * This method appends the provided error message to a log file named 'error.log'.
     * Each message is written on a new line. Useful for recording exceptions,
     * system errors, or other issues for later review and debugging.
     *
     * @param string $message The error message to log.
     *
     * @return void
     *
     * @throws \Exception If the log file cannot be written to due to
     *                    insufficient permissions or disk space issues.
     *
     * @see error.log The output log file where error messages are stored.
     */
    public static function logError(string $message)
    {
        file_put_contents('error.log', $message . PHP_EOL, FILE_APPEND);
    }

    /**
     * Logs a warning message to the warning log file.
     *
     * This method appends the provided warning message to a log file named 'warnning.log'.
     * Each message is written on a new line. Useful for recording non-critical issues,
     * potential problems, or situations that require attention but are not errors.
     *
     * @param string $message The warning message to log.
     *
     * @return void
     *
     * @throws \Exception If the log file cannot be written to due to
     *                    insufficient permissions or disk space issues.
     *
     * @see warnning.log The output log file where warning messages are stored.
     */
    public static function logWarning(string $message)
    {
        file_put_contents('warnning.log', $message . PHP_EOL, FILE_APPEND);
    }

    /**
     * Logs an informational message to the info log file.
     *
     * This method appends the provided informational message to a log file named 'info.log'.
     * Each message is written on a new line. Useful for recording general application events,
     * status updates, or other informational messages for later review.
     *
     * @param string $message The informational message to log.
     *
     * @return void
     *
     * @throws \Exception If the log file cannot be written to due to
     *                    insufficient permissions or disk space issues.
     *
     * @see info.log The output log file where informational messages are stored.
     */
    public static function logInfo(string $message)
    {
        file_put_contents('info.log', $message . PHP_EOL, FILE_APPEND);
    }
}
