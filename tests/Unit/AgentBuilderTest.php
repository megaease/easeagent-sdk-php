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

use Easeagent\AgentBuilder;
use Easeagent\Spec;
use PHPUnit\Framework\TestCase;
use Zipkin\Propagation\DefaultSamplingFlags;

final class AgentBuilderTest extends TestCase
{
    public function testBuild()
    {
        $builder = new AgentBuilder();
        $builder->havingSpec(Spec::loadFromYaml(__DIR__ . "/agent_test_normal.yml"));
        $agent = $builder->build();
        self::assertNotNull($agent);
        $builder->havingSpec(Spec::new());
        $agent = $builder->build();
        self::assertNotNull($agent);
        for ($x = 0; $x <= 10; $x++) {
            $extractedContext = DefaultSamplingFlags::createAsEmpty();
            $span = $agent->getTracing()->getTracer()->newTrace($extractedContext);
            self::assertFalse($span->isNoop());
            $span->abandon();
        }

        $spec = Spec::new();
        $spec->tracingEnable = false;
        $builder->havingSpec($spec);
        $agent = $builder->build();
        self::assertNotNull($agent);
        for ($x = 0; $x <= 10; $x++) {
            $extractedContext = DefaultSamplingFlags::createAsEmpty();
            $span = $agent->getTracing()->getTracer()->newTrace($extractedContext);
            self::assertTrue($span->isNoop());
            $span->abandon();
        }

        $spec = Spec::new();
        $spec->sampleRate = 0.0;
        $builder->havingSpec($spec);
        $agent = $builder->build();
        self::assertNotNull($agent);
        for ($x = 0; $x <= 10; $x++) {
            $extractedContext = DefaultSamplingFlags::createAsEmpty();
            $span = $agent->getTracing()->getTracer()->newTrace($extractedContext);
            self::assertTrue($span->isNoop());
            $span->abandon();
        }

        $spec = Spec::new();
        $spec->sampleRate = 0.5;
        $builder->havingSpec($spec);
        $agent = $builder->build();
        self::assertNotNull($agent);
        $isNoop = 0;
        for ($x = 0; $x <= 10; $x++) {
            $extractedContext = DefaultSamplingFlags::createAsEmpty();
            $span = $agent->getTracing()->getTracer()->newTrace($extractedContext);
            if ($span->isNoop()) {
                $isNoop++;
            }
            $span->abandon();
        }
        self::assertLessThan(10, $isNoop);
        self::assertGreaterThan(0, $isNoop);
    }
}
