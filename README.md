# easeagent-sdk-go

- [easeagent-sdk-go](#easeagent-sdk-go)
  - [Overview](#overview)
    - [Principles](#principles)
  - [Features](#features)
  - [QuickStart](#quickstart)
    - [1. Installing via Composer](#1-installing-via-composer)
    - [2. Use](#2-use)
    - [3. Init Agent](#3-init-agent)
    - [4. Transaction php](#4-transaction-php)
  - [Example](#example)
  - [About MegaCloud](#about-megacloud)


A lightweight & opening PHP SDK for Cloud-Native and APM system
## Overview

- EaseAgent SDK can collect distributed application tracing, which could be used in the APM system and improve the observability of a distributed system. for the tracing, EaseAgent SDK follows the [Google Dapper](https://research.google/pubs/pub36356/) paper. 
- EaseAgent SDK also can work with Cloud-Native architecture.
- EaseAgent SDK also can work with [MegaCloud](https://cloud.megaease.com/). For example, it can monitor for service by PHP Docker APP.

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
$agent = AgentBuilder::buildFromYaml('');//by default: Console Reporter
```

### 4. Transaction php
```php
$agent->serverTransaction(function ($span) use ($agent) {
    echo "<p>Welcome to PHP</p>";
});
```

## Example
Obs. for a more complete router/frontend/backend example, check [this repository](https://github.com/megaease/easeagent-sdk-php-example)

## About MegaCloud 
1. [Use SDK in MegaCloud](./doc/how-to-use.md)
2. Get MegaCloud Config. [About MegaCloud Config](./doc/megacloud-config.md)
3. [Decorate the Span](./doc/middleware-span.md). please use api: `Agent.startMiddlewareSpan` for decorate Span.
