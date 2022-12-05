<?php

declare(strict_types=1);

namespace Easeagent\Constant;

const ERROR = 'ERROR';
const UNKNOWN = 'unknown';
const HTTP_TAG_ATTRIBUTE_ROUTE = "http.route";
const HTTP_TAG_METHOD = "http.method";
const HTTP_TAG_PATH = "http.path";
const HTTP_TAG_STATUS_CODE = "http.status_code";
const HTTP_TAG_SCRIPT_FILENAME = "http.script.filename";
const HTTP_TAG_CLIENT_ADDRESS = "Client Address";

const MYSQL_TAG_SQL = "sql";
const MYSQL_TAG_URL = "url";

const REDIS_TAG_METHOD = "redis.method";

const ELASTICSEARCH_TAG_INDEX = "es.index";
const ELASTICSEARCH_TAG_OPERATION = "es.operation";
const ELASTICSEARCH_TAG_BODY = "es.body";

const KAFKA_TAG_TOPIC = "kafka.topic";
const KAFKA_TAG_KEY = "kafka.key";
const KAFKA_TAG_BROKER = "kafka.broker";

const RABBIT_TAG_EXCHANGE = "rabbit.exchange";
const RABBIT_TAG_ROUTING_KEY = "rabbit.routing_key";
const RABBIT_TAG_QUEUE = "rabbit.queue";
const RABBIT_TAG_BROKER = "rabbit.broker";

const MONGODB_TAG_COMMAND = "mongodb.command";
const MONGODB_TAG_COLLECTION = "mongodb.collection";
const MONGODB_TAG_CLUSTER_ID = "mongodb.cluster_id";
