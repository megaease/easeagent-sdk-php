<?php

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
        $builder->havingSpec(Spec::loadFromYaml(__DIR__ . "/agent_test_1.yml"));
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
