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


namespace EaseagentTests\Unit\Http;

use Easeagent\HTTP\HttpUtils;
use PHPUnit\Framework\TestCase;
use Zipkin\Propagation\TraceContext;
use Zipkin\RealSpan;
use Zipkin\Recorder;

final class HttpUtilsTest extends TestCase
{

    public function testSaveHttpServerInfos()
    {
        $context = TraceContext::create('186f11b67460db4d', '186f11b67460db4d');
        $span = new RealSpan($context, Recorder::createAsNoop());
        HttpUtils::saveHttpServerInfos($span);
        HttpUtils::finishSpan($span, "GET", "/test", 200);
        $span->abandon();
        self::assertTrue(true);
    }
    public function testGetServerPar()
    {
        self::assertNull(HttpUtils::getServerPar("SERVER_HOST"));
        self::assertEquals("127.0.0.1", HttpUtils::getServerPar("SERVER_HOST", "127.0.0.1"));
        $_SERVER["SERVER_HOST"] = "127.0.0.1";
        self::assertEquals("127.0.0.1", HttpUtils::getServerPar("SERVER_HOST"));
    }
    public function testGetServerParAsInt()
    {
        self::assertNull(HttpUtils::getServerParAsInt("PORT"));
        self::assertEquals(12, "12");
        self::assertEquals(12, HttpUtils::getServerParAsInt("PORT", "12"));
        $_SERVER["PORT"] = "13";
        self::assertEquals(13, HttpUtils::getServerParAsInt("PORT"));
    }

    public function testGetServerParToupper()
    {
        self::assertNull(HttpUtils::getServerParToupper("METHOD"));
        self::assertEquals("GET", HttpUtils::getServerParToupper("METHOD", "get"));
        $_SERVER["METHOD"] = "post";
        self::assertEquals("POST", HttpUtils::getServerParToupper("METHOD"));
    }
}
