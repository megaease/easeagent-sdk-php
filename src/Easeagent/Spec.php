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
                    $spec->serviceName = $val;
                    break;
                case "tracing_type":
                    $spec->tracingType = $val;
                    break;
                case "tracing.enable":
                    $spec->tracingEnable = $val;
                    break;
                case "tracing.sample.rate":
                    $spec->sampleRate = $val;
                    break;
                case "tracing.shared.spans":
                    $spec->sharedSpans = $val;
                    break;
                case "tracing.id128bit":
                    $spec->id128bit = $val;
                    break;
                case "reporter.output.server":
                    $spec->outputServerUrl = $val;
                    break;
                case "reporter.output.server.tls.enable":
                    $spec->enableTls = $val;
                    break;
                case "reporter.output.server.tls.key":
                    $spec->tlsKey = $val;
                    break;
                case "reporter.output.server.tls.cert":
                    $spec->tlsCert = $val;
                    break;
                default:
                    break;
            }
        }
        return $spec;
    }

    public static function counter() {
        static $counter = 0;
        $counter++;
        return $counter;
    }
}
