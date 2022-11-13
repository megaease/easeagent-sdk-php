<?php

declare(strict_types=1);

namespace Easeagent;

use Symfony\Component\Yaml\Yaml;

class Spec
{
    public string $outputServerUrl;
    public bool $enableTls;
    public string $tlsKey;
    public string $tlsCert;
    public string $serviceName;
    public string $tracingType;

    public bool $tracingEnable;
    public float $sampleRate;
    public bool $sharedSpans;
    public bool $id128bit;

    public static function loadFromYaml(string $yamlPath): Spec
    {
        $yaml = Yaml::parse(file_get_contents($yamlPath), Yaml::PARSE_OBJECT_FOR_MAP);
        $spec = new Spec;
        foreach ($yaml as $key => $val) {
            // echo "key: ".$key." value: ".$val;
            switch ($key) {
                case "service_name":
                    $spec->serviceName = $val == null ? "zone.damoin.service" : $val;
                    break;
                case "tracing_type":
                    $spec->tracingType = $val == null ? "log-tracing" : $val;
                    break;
                case "tracing.enable":
                    $spec->tracingEnable = $val == null ? true : $val;
                    break;
                case "tracing.sample.rate":
                    $spec->sampleRate =  $val == null ? 1.0 : $val;
                    break;
                case "tracing.shared.spans":
                    $spec->sharedSpans =  $val == null ? true : $val;
                    break;
                case "tracing.id128bit":
                    $spec->id128bit =  $val == null ? false : $val;
                    break;
                case "reporter.output.server":
                    $spec->outputServerUrl =  $val == null ? "http://localhost:9411/api/v2/spans" : $val;
                    break;
                case "reporter.output.server.tls.enable":
                    $spec->enableTls = $val == null ? false : $val;
                    break;
                case "reporter.output.server.tls.key":
                    $spec->tlsKey = $val == null ? "" : $val;
                    break;
                case "reporter.output.server.tls.cert":
                    $spec->tlsCert = $val == null ? "" : $val;
                    break;
                default:
                    break;
            }
        }
        return $spec;
    }
}
