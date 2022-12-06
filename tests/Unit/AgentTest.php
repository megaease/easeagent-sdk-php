<?php

namespace EaseagentTests\Unit;

use Easeagent\AgentBuilder;
use Easeagent\Middleware\Type;
use Easeagent\Spec;
use PHPUnit\Framework\TestCase;

final class AgentTest extends TestCase
{
    public function testSpan()
    {
        $builder = new AgentBuilder();
        $agent = $builder->havingSpec(Spec::new())->build();
        self::assertNotNull($agent);
        $agent->serverReceive(function ($span) use ($agent) {
            self::assertNotNull($span);
            $context = $span->getContext();
            self::assertNull($context->getParentId());
            $childSpan = $agent->startClientSpan($span, "child");
            self::assertEquals($span->getContext()->getTraceId(), $childSpan->getContext()->getParentId());
            $childSpan->finish();

            $childSpan = $agent->startMiddlewareSpan($span, "mysql", Type::MySql);
            self::assertEquals($span->getContext()->getTraceId(), $childSpan->getContext()->getParentId());
            $childSpan->finish();
        });
    }
}
