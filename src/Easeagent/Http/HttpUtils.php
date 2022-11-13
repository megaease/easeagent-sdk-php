<?php

declare(strict_types=1);

namespace Easeagent\HTTP;

use Zipkin\Span;

const TRACING_ATTRIBUTE_ROUTE = "http.route";
const TRACING_HTTP_TAG_METHOD = "http.method";
const TRACING_HTTP_TAG_PATH = "http.path";
const TRACING_HTTP_TAG_ERROR = "error";
const TRACING_HTTP_TAG_STATUS_CODE = "http.status_code";

final class HttpUtils
{
    public static function saveInfos(Span $span, string $method, string $path, int $statusCode): Span
    {
        $span->setName($method);
        $span->tag(TRACING_HTTP_TAG_METHOD, strtoupper($method));
        $span->tag(TRACING_HTTP_TAG_PATH, $path);
        $span->tag(TRACING_ATTRIBUTE_ROUTE, strtoupper($method) . " " . $path);
        $name = HttpUtils::catchAllName($method, $statusCode);
        if ($name !== "") {
            $span->setName($name);
        }
        if ($statusCode < 100 || $statusCode > 399) {
            $span->tag(TRACING_HTTP_TAG_ERROR, strval($statusCode));
        }
        if ($statusCode < 200 || $statusCode > 299) { // not success code
            $span->tag(TRACING_HTTP_TAG_STATUS_CODE, strval($statusCode));
        }
        return $span;
    }

    private static function catchAllName(String $method, int $statusCode): string
    {
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
}