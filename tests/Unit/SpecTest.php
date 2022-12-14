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


namespace EaseagentTests\Unit;

use Easeagent\Spec;
use PHPUnit\Framework\TestCase;

final class SpecTest extends TestCase
{
    public function testNew()
    {
        $spec = Spec::new();
        $spec->serviceName = "zone.damoin.service";
        $spec->tracingType = "log-tracing";
        $spec->tracingEnable = true;
        $spec->sampleRate = 1.0;
        $spec->sharedSpans = true;
        $spec->id128bit = false;
        $spec->outputServerUrl = "";
        $spec->enableTls = false;
        $spec->tlsKey = "";
        $spec->tlsCert = "";

        $this->assertEquals("zone.damoin.service", $spec->serviceName);
        $this->assertEquals("log-tracing", $spec->tracingType);
        $this->assertTrue($spec->tracingEnable);
        $this->assertEquals(1.0, $spec->sampleRate);
        $this->assertTrue($spec->sharedSpans);
        $this->assertFalse($spec->id128bit);
        $this->assertEquals("", $spec->outputServerUrl);
        $this->assertFalse($spec->enableTls);
        $this->assertEquals("", $spec->tlsKey);
        $this->assertEquals("", $spec->tlsCert);
    }

    public function testLoadFromYaml()
    {
        $spec = Spec::loadFromYaml("");
        $this->assertEquals("zone.damoin.service", $spec->serviceName);
        $spec = Spec::loadFromYaml(__DIR__ . "/agent_test_normal.yml");
        $this->assertEquals("demo.demo.sdk-php-router-service", $spec->serviceName);
        $this->assertEquals("log-tracing", $spec->tracingType);
        $this->assertTrue($spec->tracingEnable);
        $this->assertEquals(0.5, $spec->sampleRate);
        $this->assertTrue($spec->sharedSpans);
        $this->assertFalse($spec->id128bit);
        $this->assertEquals("http://localhost:9411/api/v2/spans", $spec->outputServerUrl);
        $this->assertTrue($spec->enableTls);
        // \Easeagent\Log\Log::getLogger()->addInfo($spec->tlsKey);
        $this->assertEquals("----------- key -----------\nkey content\n----------- key end -----------\n", $spec->tlsKey);
        $this->assertEquals("----------- cert -----------\ncert content\n----------- cert end -----------\n", $spec->tlsCert);

        $spec = Spec::loadFromYaml(__DIR__ . "/agent_test_empty_tls.yml");
        $this->assertEquals("demo.demo.sdk-php-router-service", $spec->serviceName);

        $spec = Spec::loadFromYaml(__DIR__ . "/agent_test_just_service_name.yml");
        $this->assertEquals("demo.demo.sdk-php-router-service", $spec->serviceName);
        $this->assertEquals("log-tracing", $spec->tracingType);
        $this->assertTrue($spec->tracingEnable);
        $this->assertEquals(1.0, $spec->sampleRate);
        $this->assertTrue($spec->sharedSpans);
        $this->assertFalse($spec->id128bit);
        $this->assertEquals("", $spec->outputServerUrl);
        $this->assertFalse($spec->enableTls);
        $this->assertEquals("", $spec->tlsKey);
        $this->assertEquals("", $spec->tlsCert);
    }
}
