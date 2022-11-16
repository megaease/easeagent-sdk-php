<?php

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
