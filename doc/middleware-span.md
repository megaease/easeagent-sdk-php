# Decorate spans with attributes

This document will explain how to add attributes to trace data sent to the MegaCloud so that spans display specific properties in the UI.

## Why decorate your spans?

When you send data to our MegaCloud, you can add custom attributes to spans. For example, you might decide to add tags like order.id or user.id in order to help you analyze your trace data.

Some expected attribute values cause our distributed tracing UI to display some specific trace properties and details.

For example, if a span has a tag with an error, the UI displays that span with an error.

For another example, a span with a tag that has a `component.type=database` will be displayed as a datastore span in the UI.

Decorating your spans to show specific properties in our UI can help you:

* Better understand the structure of your trace data.
* More easily troubleshoot problems.

## How to decorate your spans for Middleware?

This table explains how to get `spans` sent to the MegaCloud to show up with specific `Middleware` in the UI.

Use a `tag` with a key `component.type`，Different types represent different `Middleware`. The following is a list of special middleware for `MegaCloud`.

|  Middleware   |     value     |
| ------------- | ------------- |
| MySql         | database      |
| Redis         | redis         |
| ElasticSearch | elasticsearch |
| Kafka         | kafka         |
| RabbitMQ      | rabbitmq      |
| MongoDB       | mongodb       |
| Motan         | motan         |

Use a remote ServiceName with an `{type}-{name}` prefix.  When no `name`, just `type`. The following is a list of special middleware remote ServiceName for `MegaCloud`.

|  Middleware   |       value       |
| ------------- | ----------------- |
| MySql         | mysql-{db_name}   |
| Redis         | redis             |
| ElasticSearch | elasticsearch     |
| Kafka         | kafka             |
| RabbitMQ      | rabbitmq          |
| MongoDB       | mongodb-{db_name} |
| Motan         | motan             |

### How to decorate your spans with tags?

Use a tag key with an `{type}.` prefix. like `http.method`.