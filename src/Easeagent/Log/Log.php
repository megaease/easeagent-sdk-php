<?php
/**
 * Copyright 2022 MegaEase
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


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
