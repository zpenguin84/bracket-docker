<?php

namespace Application;

class Log
{
    private const TMP_DIR = __DIR__ . '/../tmp';

    public static function write($message)
    {
        if (!is_dir(self::TMP_DIR))
        {
            mkdir(self::TMP_DIR);
        }
        $message = date('Y-m-d H:i:s') . "\t\t" . $message . "\n";
        $logFile = self::TMP_DIR . '/eventlog.txt';
        file_put_contents($logFile, $message, FILE_APPEND);
    }
}