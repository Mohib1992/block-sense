<?php
namespace BlockSense\Util;

class Logger {
    public static function logFraud(array $tx): void {
        file_put_contents('fraud.log', json_encode($tx) . PHP_EOL, FILE_APPEND);
    }
}