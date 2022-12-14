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

namespace Easeagent\Middleware;

const TAG = "component.type";
enum Type
{
    case MySql;
    case Redis;
    case ElasticSearch;
    case Kafka;
    case RabbitMQ;
    case MongoDB;
    case Motan;

    function value(): string
    {
        $result = "";
        match ($this) {
            Type::MySql => $result = "database",
            Type::Redis => $result = "redis",
            Type::ElasticSearch => $result = "elasticsearch",
            Type::Kafka => $result = "kafka",
            Type::RabbitMQ => $result = "rabbitmq",
            Type::MongoDB => $result = "mongodb",
            Type::Motan => $result = "motan",
        };
        return $result;
    }
}
