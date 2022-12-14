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

namespace Easeagent;

use Easeagent\HTTP\HttpUtils;
use Zipkin\TracingBuilder;
use Zipkin\Samplers\PercentageSampler;
use Zipkin\Endpoint;
use Easeagent\Reporters\JsonV2Serializer;
use Easeagent\Reporters\Http\CurlFactory;
use Zipkin\Reporters\Http;
use Zipkin\Reporters\Log;
use Zipkin\Samplers\BinarySampler;

class AgentBuilder
{
    private ?Spec $spec;
    private ?string $localServiceIPv4;
    private ?int $localServicePort;

    public function __construct()
    {
        $this->localServiceIPv4 = HttpUtils::getServerPar(\Easeagent\HTTP\SERVER_NAME);
        $this->localServicePort = HttpUtils::getServerParAsInt(\Easeagent\HTTP\SERVER_PORT);
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
        $serializer  = new JsonV2Serializer($this->spec->serviceName, $this->spec->tracingType);
        $reporter = null;
        if ($this->spec->outputServerUrl == "") {
            $reporter = new Log(\Easeagent\Log\Log::getLogger(), $serializer);
        } else {
            $reporter = new Http($configs, CurlFactory::create(), \Easeagent\Log\Log::getLogger(), $serializer);
        }

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

    /**
     * build a Agent from a config file.
     * @return Agent
     */
    public static function buildFromYaml(string $configPath): Agent
    {
        $agentBuilder = new AgentBuilder;
        $agentBuilder->havingSpec(Spec::loadFromYaml($configPath));
        return $agentBuilder->build();
    }
}
