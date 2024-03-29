# MegaEase Cloud Configuration

Modify the [agent.yml](https://github.com/megaease/easeagent-sdk-php/blob/main/doc/agent.yml) file to configure your information.

## 1. Name

You'll serviceName to find your data later. It's important to use a unique and meaningful name.

The serviceName of MegaEase Cloud consists of three parts: zone, domain, name. They are joined by `.` into `ServiceName`

```yaml
serviceName: zone.domain.service
```

## 2. Reporter

MegaEase Cloud uses http to receive data, so you need to change the configuration to http and MegaEase Cloud's address.
```yaml
reporter.output.server: {MEGAEASE_CLOUD_URL}/application-tracing-log
```
## 3. MTLS

MTLS is a secure authentication protocol for EaseAgent to connect to MegaEase Cloud.

Config: Get TLS
```yaml
reporter.output.server.tls.enable: true
reporter.output.server.tls.key: YOUR_TLS_KEY
reporter.output.server.tls.cert: YOUR_TLS_CERT
```

## 4. Tracing
we have some
```yaml
tracing.type: log-tracing
tracing.enable: true
tracing.sample.rate: 1.0
```

## Third: About MegaEase Cloud

### Download agent.yml

Login: [MegaEase Cloud](https://cloud.megaease.com/) -> `[stack]`->`[document]`->`[PHP]`

1. Input `zone`, `domain`, `service`
2. Input `Sampled` if you want
3. Click `Submit And Download`

### About `MEGAEASE_CLOUD_URL` And `TLS`
When you download the `agent.yml` file through our MegaEase Cloud, `MEGAEASE_CLOUD_URL` and `TLS` will be filled in for you automatically.

If you need it separately, please download the `agent.yml` and get it by yourself.