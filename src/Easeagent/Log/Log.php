<?php

declare(strict_types=1);

namespace Easeagent\Log;

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;


class Log
{


    static ?Logger $logger = null;
    static function getLogger(): Logger
    {
        if (self::$logger == null) {
            self::$logger = self::newLogger();
        }
        return self::$logger;
    }

    static function newLogger(): Logger
    {
        $logger = new Logger('log');
        $handler = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Logger::DEBUG, true, false);
        $logger->pushHandler($handler);
        return $logger;
    }
}
