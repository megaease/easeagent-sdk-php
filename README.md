# easeagent-sdk-php

First ***production ready***, simple and full Easeagent SDK implementation without dependencies.

## Installing via Composer

easeagent-sdk-php can be installed via Composer:
```bash
$ composer require megaease/easeagent-sdk-php
```

## Usage
### First: Configuration
create a yaml file config for your server like this: [agent.yml](./agent.yml)

If you are using `MegaCloud`. Please download the agent.yaml on the front end. `YOUR_SERVCIE_NAME`,`TYPE_OF_TRACING`,`MEGA_CLOUD_URL` and `TLS` will be filled in for you automatically.

### Second: Init Agent

##### 1. use class
```php
use Easeagent\AgentBuilder;
use Easeagent\HTTP\HttpUtils;
use Zipkin\Timestamp;
```

##### 2. Init Agent
You can load spec then new Agent like below code:
```php
$agent = AgentBuilder::buildFromYaml(getenv('EASEAGENT_SDK_CONFIG_FILE'));
```

### Third: use server transaction and client span

##### 1. Server Transaction
```php
$agent->serverTransaction(function ($span) use ($agent){
    //do your business
    usleep(50000);
});
```

##### 2. Client Span
```php
// --------------------- http client ----------------------
/* Creates the span for getting the users list */
$childSpan = $agent->startClientSpan($span, 'users:get_list');
/* Injects the context into the wire */
$headers = $agent->injectorHeaders($childSpan);
/* HTTP Request to the backend */
$httpClient = new Client();
$request = new \GuzzleHttp\Psr7\Request('POST', 'localhost:9000', $headers);
$childSpan->annotate('request_started', Timestamp\now());
$response = $httpClient->send($request);
$childSpan->annotate('request_finished', Timestamp\now());

/* Save Request info and finish span */
HttpUtils::finishSpan($childSpan, $request->getMethod(), $request->getUri()->getPath(), $response->getStatusCode());

// --------------------- mysql client ----------------------
$mysqlSpan = $agent->startClientSpan($span, 'user:get_list:mysql_query');
$childSpan->setRemoteEndpoint(\Zipkin\Endpoint::create("mysql"));
usleep(50000);
$mysqlSpan->finish();
```

Obs. for a more complete router/frontend/backend example, check [this repository](https://github.com/megaease/easeagent-sdk-php-example)