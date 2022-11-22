<?php

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
        self::assertNull(HttpUtils::getServerPar("aaa"));
        self::assertEquals("bbb", HttpUtils::getServerPar("aaa", "bbb"));
        $_SERVER["aaa"] = "testValue";
        self::assertEquals("testValue", HttpUtils::getServerPar("aaa"));
    }
    public function testGetServerParAsInt()
    {
        self::assertNull(HttpUtils::getServerParAsInt("aaa"));
        self::assertEquals(12, "12");
        self::assertEquals(12, HttpUtils::getServerParAsInt("aaa", "12"));
        $_SERVER["aaa"] = "13";
        self::assertEquals(13, HttpUtils::getServerParAsInt("aaa"));
    }

    public function testGetServerParToupper()
    {
        self::assertNull(HttpUtils::getServerParToupper("aaa"));
        self::assertEquals("TEST", HttpUtils::getServerParToupper("aaa", "test"));
        $_SERVER["aaa"] = "test_value";
        self::assertEquals("TEST_VALUE", HttpUtils::getServerParToupper("aaa"));
    }
}
