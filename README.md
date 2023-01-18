# easeagent-sdk-php

- [easeagent-sdk-php](#easeagent-sdk-php)
  - [Overview](#overview)
    - [Principles](#principles)
  - [Features](#features)
  - [QuickStart](#quickstart)
    - [1. Installing via Composer](#1-installing-via-composer)
    - [2. Use](#2-use)
    - [3. Init Agent](#3-init-agent)
    - [4. Server Span](#4-server-span)
  - [Documentation](#documentation)
  - [Example](#example)
  - [About MegaEase Cloud](#about-megaease-cloud)
  - [Community](#community)
  - [Licenses](#licenses)


A lightweight & opening PHP SDK for Cloud-Native and APM system
## Overview

- EaseAgent SDK can collect distributed application tracing, which could be used in the APM system and improve the observability of a distributed system. for the tracing, EaseAgent SDK follows the [Google Dapper](https://research.google/pubs/pub36356/) paper and use [zipkin-php](https://github.com/openzipkin/zipkin-php) core library. 
- EaseAgent SDK also can work with Cloud-Native architecture.
- EaseAgent SDK also can work with [MegaEase Cloud](https://cloud.megaease.com/). For example, it can monitor for service by PHP Docker APP.

### Principles
- Safe to PHP application/service.
- Highly extensible, users can easily do extensions through the api
- Design for Micro-Service architecture, collecting the data from a service perspective.

## Features
* Easy to use. It is right out of the box for Http Server Tracing.
  * Collecting Tracing Logs.
    * Http Server
    * Client
  * Decorate the Span API for Middleware

* Data Reports
  * Console Reporter.
  * Http Reporter.

* Standardization
    * The tracing data format is fully compatible with the Zipkin data format.

## QuickStart
First ***production ready***, simple and full Easeagent SDK implementation without dependencies.
### 1. Installing via Composer

easeagent-sdk-php can be installed via Composer:
```bash
$ composer require megaease/easeagent-sdk-php
```

### 2. Use
```php
use Easeagent\AgentBuilder;
```

### 3. Init Agent
```php
// new tracing agent from yaml file.
// If you want to publish the `docker app` through the `cloud of megaease` and send the monitoring data to the `cloud`, 
// please obtain the configuration file path through the environment variable `EASEAGENT_CONFIG`.
// We will pass it to you the `cloud configuration` file path.
$agent = AgentBuilder::buildFromYaml(getenv('EASEAGENT_CONFIG'));//by default: Console Reporter
```

### 4. Server Span
```php
$agent->serverReceive(function ($span) use ($agent) {
    echo "<p>Welcome to PHP</p>";
});
```
## Documentation
[About Config](./doc/about-config.md)
## Example
Obs. for a more complete router/frontend/backend example, check [this repository](https://github.com/megaease/easeagent-sdk-php-example)

## About MegaEase Cloud 
1. [Use SDK in MegaEase Cloud](./doc/how-to-use.md)
2. Get MegaEase Cloud Config. [About MegaEase Cloud Config](./doc/megaease-cloud-config.md)
3. [Decorate the Span](./doc/middleware-span.md). please use api: `Agent.startMiddlewareSpan` for decorate Span.

## Community

* [Github Issues](https://github.com/megaease/easeagent-sdk-php/issues)
* [Join Slack Workspace](https://join.slack.com/t/openmegaease/shared_invite/zt-upo7v306-lYPHvVwKnvwlqR0Zl2vveA) for requirement, issue and development.
* [MegaEase on Twitter](https://twitter.com/megaease)

If you have any questions, welcome to discuss them in our community. Welcome to join!


## Licenses
EaseAgent PHP SDK is licensed under the Apache License, Version 2.0. See [LICENSE](./LICENSE) for the full license text.
