<?php

declare(strict_types=1);

namespace Easeagent;

use Symfony\Component\Yaml\Yaml;
use Throwable;

use  Easeagent\Log\Log;

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

    public static function new(): Spec
    {
        $spec = new Spec;
        $spec->serviceName = "zone.damoin.service";
        $spec->tracingType = "log-tracing";
        $spec->tracingEnable = true;
        $spec->sampleRate = 1.0;
        $spec->sharedSpans = true;
        $spec->id128bit = false;
        $spec->outputServerUrl = "";  // empty output is logger span json
        $spec->enableTls = false;
        $spec->tlsKey = "";
        $spec->tlsCert = "";
        return $spec;
    }

    public static function loadFromYaml(string $yamlPath): Spec
    {
        $logger = Log::getLogger();
        $spec = self::new();
        if (!file_exists($yamlPath)) {
            return $spec;
        }
        try {
            $yaml = Yaml::parse(file_get_contents($yamlPath), Yaml::PARSE_OBJECT_FOR_MAP);
        } catch (Throwable $e) {
            $logger->addError("load yaml file error: " . $e->getMessage());
            return $spec;
        }

        $logger->info("load spec from yaml: ".$yamlPath);

        foreach ($yaml as $key => $val) {
            // echo "key: ".$key." value: ".$val;
            switch ($key) {
                case "service_name":
                    $spec->serviceName = $val == null ? "" : $val;
                    break;
                case "tracing_type":
                    $spec->tracingType = $val == null ? "" : $val;
                    break;
                case "tracing.enable":
                    $spec->tracingEnable = $val;
                    break;
                case "tracing.sample.rate":
                    $spec->sampleRate =  $val;
                    break;
                case "tracing.shared.spans":
                    $spec->sharedSpans =  $val;
                    break;
                case "tracing.id128bit":
                    $spec->id128bit =  $val;
                    break;
                case "reporter.output.server":
                    $spec->outputServerUrl = $val == null ? "" : $val;
                    break;
                case "reporter.output.server.tls.enable":
                    $spec->enableTls = $val;
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
