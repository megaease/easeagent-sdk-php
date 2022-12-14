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
