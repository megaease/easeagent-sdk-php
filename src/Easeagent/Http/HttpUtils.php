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

namespace Easeagent\HTTP;

use Zipkin\Span;

const SERVER_NAME = 'SERVER_NAME';
const SERVER_PORT = 'SERVER_PORT';
const REQUEST_METHOD = 'REQUEST_METHOD';
const REQUEST_URI = 'REQUEST_URI';
const REMOTE_ADDR = 'REMOTE_ADDR';
const REMOTE_PORT = 'REMOTE_PORT';
const SCRIPT_FILENAME = 'SCRIPT_FILENAME';

final class HttpUtils
{

    public static function finishSpan(Span $span, string $method, string $path, int $statusCode)
    {
        self::saveInfos($span, $method, $path, $statusCode);
    }
    public static function saveInfos(Span $span, string $method, string $path, int $statusCode): Span
    {
        $span->setName($method);
        $span->tag(\Easeagent\Constant\HTTP_TAG_METHOD, strtoupper($method));
        $span->tag(\Easeagent\Constant\HTTP_TAG_PATH, $path);
        $span->tag(\Easeagent\Constant\HTTP_TAG_ATTRIBUTE_ROUTE, strtoupper($method) . " " . $path);
        $name = HttpUtils::catchAllName($method, $statusCode);
        if ($name !== "") {
            $span->setName($name);
        }
        // reference https://github.com/openzipkin/zipkin-go/blob/master/middleware/http/client.go#L139
        if ($statusCode < 100 || $statusCode > 399) {
            $span->tag(\Easeagent\Constant\ERROR, strval($statusCode));
        }
        if ($statusCode < 200 || $statusCode > 299) {
            $span->tag(\Easeagent\Constant\HTTP_TAG_STATUS_CODE, strval($statusCode));
        }
        return $span;
    }

    private static function catchAllName(String $method, int $statusCode): string
    {
        // reference https://github.com/megaease/easeagent/blob/master/plugin-api/src/main/java/com/megaease/easeagent/plugin/tools/trace/HttpUtils.java#L78
        switch ($statusCode) {
            // from https://tools.ietf.org/html/rfc7231#section-6.4
            case 301:
            case 302:
            case 303:
            case 305:
            case 306:
            case 307:
                return $method . " redirected";
            case 404:
                return $method . " not_found";
            default:
                return "";
        }
    }

    public static function saveHttpServerInfos(Span $span)
    {
        $span->tag(\Easeagent\Constant\HTTP_TAG_METHOD, self::getServerParToupper(REQUEST_METHOD, \Easeagent\Constant\UNKNOWN));
        $span->tag(\Easeagent\Constant\HTTP_TAG_PATH, self::getServerPar(REQUEST_URI, \Easeagent\Constant\UNKNOWN));
        if (self::getServerParToupper(REQUEST_METHOD) !== null || self::getServerPar(REQUEST_URI) !== null) {
            $span->tag(\Easeagent\Constant\HTTP_TAG_ATTRIBUTE_ROUTE, self::getServerParToupper(REQUEST_METHOD, "") . " " . self::getServerPar(REQUEST_URI, ""));
        }
        if (self::getServerPar(REMOTE_ADDR) !== null || self::getServerPar(REMOTE_PORT) !== null) {
            $span->tag(\Easeagent\Constant\HTTP_TAG_CLIENT_ADDRESS, self::getServerPar(REMOTE_ADDR, "") . ":" . self::getServerPar(REMOTE_PORT, ""));
        }
        $span->tag(\Easeagent\Constant\HTTP_TAG_SCRIPT_FILENAME, self::getServerPar(SCRIPT_FILENAME, \Easeagent\Constant\UNKNOWN));
    }

    public static function getServerPar(string $key, ?string $default = null): ?string
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        return $default;
    }

    public static function getServerParAsInt(string $key, ?int $default = null): ?int
    {
        if (isset($_SERVER[$key])) {
            return intval($_SERVER[$key]);
        }
        if ($default == null) {
            return $default;
        }
        return intval($default);
    }
    public static function getServerParToupper(string $key, ?string $default = null): ?string
    {
        if (isset($_SERVER[$key])) {
            return strtoupper($_SERVER[$key]);
        }
        if ($default == null) {
            return $default;
        }
        return strtoupper($default);
    }
}
