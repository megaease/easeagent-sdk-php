<?php

declare(strict_types=1);

namespace Easeagent;

use Zipkin\TracingBuilder;
use Zipkin\Samplers\PercentageSampler;
use Zipkin\Endpoint;
use Easeagent\Reporters\JsonV2Serializer;
use Easeagent\Reporters\Http\CurlFactory;
use Zipkin\Reporters\Http;
use Zipkin\Samplers\BinarySampler;

class AgentBuilder
{
    private ?Spec $spec;
    private ?string $localServiceIPv4;
    private ?int $localServicePort;

    public function __construct()
    {
        $this->localServiceIPv4 = $_SERVER['SERVER_NAME'];
        $this->localServicePort = intval($_SERVER['SERVER_PORT']);
    }
    public static function create(): self
    {
        return new self();
    }

    public function havingSpec(Spec $spec): self
    {
        $this->spec = $spec;
        return $this;
    }
    public function havingLocalServiceIPv4(?string $localServiceIPv4): self
    {
        $this->localServiceIPv4 = $localServiceIPv4;
        return $this;
    }
    public function havingLocalServicePort(?int $localServicePort): self
    {
        $this->localServicePort = $localServicePort;
        return $this;
    }

    /**
     * @return Agent
     */
    public function build(): Agent
    {
        $endpoint = Endpoint::create($this->spec->serviceName, $this->localServiceIPv4, null, $this->localServicePort);
        $configs = [
            'endpoint_url' => $this->spec->outputServerUrl,
            'enable_tls' => $this->spec->enableTls,
            'tls_key' => $this->spec->tlsKey,
            'tls_cert' => $this->spec->tlsCert
        ];
        $reporter = new Http($configs, CurlFactory::create(), null, new JsonV2Serializer($this->spec->serviceName, $this->spec->tracingType));
        $sampler = null;
        if ($this->spec->tracingEnable) {
            $sampler = PercentageSampler::create($this->spec->sampleRate);
        } else {
            $sampler = BinarySampler::createAsNeverSample();
        }

        $tracing = TracingBuilder::create()
            ->havingLocalEndpoint($endpoint)
            ->havingSampler($sampler)
            ->havingReporter($reporter)
            ->havingTraceId128bits($this->spec->id128bit)
            ->build();
        return new Agent($tracing, $sampler);
    }

    public static function buildFromYaml($configPath): Agent
    {
        $agentBuilder = new AgentBuilder;
        $agentBuilder->havingSpec(Spec::loadFromYaml($configPath));
        return $agentBuilder->build();
    }
}