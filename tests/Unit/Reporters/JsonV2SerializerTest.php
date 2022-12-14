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


namespace ZipkinTests\Unit\Reporters;

use Easeagent\Reporters\JsonV2Serializer;
use Zipkin\Recording\Span;
use Zipkin\Propagation\TraceContext;
use Zipkin\Endpoint;
use PHPUnit\Framework\TestCase;

final class JsonV2SerializerTest extends TestCase
{
    private function newSpan(): Span
    {
        $context = TraceContext::create('186f11b67460db4d', '186f11b67460db4d');
        $localEndpoint = Endpoint::create('service1', '192.168.0.11', null, 3301);
        $span = Span::createFromContext($context, $localEndpoint);
        $startTime = 1594044779509687;
        $span->start($startTime);
        $span->setName('Test');
        $span->setKind('CLIENT');
        $span->tag('test_key', 'test_value');
        $span->annotate($startTime + 100, 'test_annotation');
        $span->setError(new \RuntimeException('test_error'));
        $span->finish($startTime + 1000);
        return $span;
    }

    public function testSpanIsSerializedSuccessfully()
    {
        $span = self::newSpan();
        $remoteEndpoint = Endpoint::create('SERVICE2', null, '2001:0db8:85a3:0000:0000:8a2e:0370:7334', 3302);
        $span->setRemoteEndpoint($remoteEndpoint);
        $serializer = new JsonV2Serializer("demo.demo.sdk-php-router-service", "log-tracing");
        $serializedSpans = $serializer->serialize([$span]);

        $expectedSerialization = '[{'
            . '"id":"186f11b67460db4d","traceId":"186f11b67460db4d","timestamp":1594044779509687,"name":"test",'
            . '"duration":1000,"localEndpoint":{"serviceName":"service1","ipv4":"192.168.0.11","port":3301},'
            . '"kind":"CLIENT",'
            . '"remoteEndpoint":{"serviceName":"service2","ipv6":"2001:0db8:85a3:0000:0000:8a2e:0370:7334","port":3302}'
            . ',"annotations":[{"value":"test_annotation","timestamp":1594044779509787}],'
            . '"tags":{"test_key":"test_value","error":"test_error"}'
            . ',"service":"demo.demo.sdk-php-router-service","type": "log-tracing"'
            . '}]';
        $this->assertEquals($expectedSerialization, $serializedSpans);

        $span = self::newSpan();
        $span->tag(\Easeagent\Middleware\TAG, \Easeagent\Middleware\Type::MySql->value());
        $serializedSpans = $serializer->serialize([$span]);
        $expectedSerialization = '[{'
            . '"id":"186f11b67460db4d","traceId":"186f11b67460db4d","timestamp":1594044779509687,"name":"test",'
            . '"duration":1000,"localEndpoint":{"serviceName":"service1","ipv4":"192.168.0.11","port":3301},'
            . '"kind":"CLIENT",'
            . '"remoteEndpoint":{"serviceName":"database"}'
            . ',"annotations":[{"value":"test_annotation","timestamp":1594044779509787}],'
            . '"tags":{"test_key":"test_value","component.type":"database","error":"test_error"}'
            . ',"service":"demo.demo.sdk-php-router-service","type": "log-tracing"'
            . '}]';
        $this->assertEquals($expectedSerialization, $serializedSpans);

        $span = self::newSpan();
        $remoteEndpoint = Endpoint::create("", null, '2001:0db8:85a3:0000:0000:8a2e:0370:7334', 3302);
        $span->setRemoteEndpoint($remoteEndpoint);
        $span->tag(\Easeagent\Middleware\TAG, \Easeagent\Middleware\Type::MySql->value());
        $serializedSpans = $serializer->serialize([$span]);
        $expectedSerialization = '[{'
            . '"id":"186f11b67460db4d","traceId":"186f11b67460db4d","timestamp":1594044779509687,"name":"test",'
            . '"duration":1000,"localEndpoint":{"serviceName":"service1","ipv4":"192.168.0.11","port":3301},'
            . '"kind":"CLIENT",'
            . '"remoteEndpoint":{"serviceName":"database","ipv6":"2001:0db8:85a3:0000:0000:8a2e:0370:7334","port":3302}'
            . ',"annotations":[{"value":"test_annotation","timestamp":1594044779509787}],'
            . '"tags":{"test_key":"test_value","component.type":"database","error":"test_error"}'
            . ',"service":"demo.demo.sdk-php-router-service","type": "log-tracing"'
            . '}]';
        $this->assertEquals($expectedSerialization, $serializedSpans);
    }
}
