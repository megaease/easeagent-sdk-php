<?php

declare(strict_types=1);

namespace Easeagent;

use Easeagent\HTTP\HttpUtils;
use Easeagent\Middleware\Type;
use Zipkin\Span;
use Zipkin\Tracing;
use Zipkin\Propagation\Map;
use Zipkin\Propagation\DefaultSamplingFlags;
use Exception;

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

class Agent
{
    private Tracing $tracing;
    public function __construct(Tracing $tracing)
    {
        $this->tracing = $tracing;
        $agent = $this;
        register_shutdown_function(function () use ($agent) {
            $agent->flush();
        });
    }

    /**
     * get zipkin Tracing Object.
     *
     * @return Tracing
     */
    public function getTracing(): Tracing
    {
        return $this->tracing;
    }


    /**
     * Calling this will flush any pending spans to the transport.
     * use zipkin tracer
     */
    public function flush()
    {
        $this->tracing->getTracer()->flush();
    }

    /**
     * Obtain key:value from the request passed by a parent server and create a Span. Then call callable. 
     * @param $callback php server service call.
     */
    public function serverReceive(callable $callback)
    {
        $span = $this->startServerSpan(HttpUtils::getServerPar(\Easeagent\HTTP\REQUEST_METHOD, \Easeagent\Constant\UNKNOWN));
        try {
            return $callback($span);
        } catch (Exception $e) {
            $span->setError($e);
            throw $e;
        } finally {
            $span->finish();
        }
    }

    /**
     * Obtain key:value from the request passed by a parent server and create a Span.
     * @return Span
     */
    public function startServerSpan(string $name): Span
    {
        /* Extracts the context from the HTTP headers */
        $extractor = $this->tracing->getPropagation()->getExtractor(new Map());
        $extractedContext = $extractor(getallheaders());

        $tracer = $this->tracing->getTracer();
        if ($extractedContext->isEmpty()) {
            $extractedContext = DefaultSamplingFlags::createAsEmpty();
            $span = $tracer->newTrace($extractedContext);
        } else {
            $span = $tracer->joinSpan($extractedContext);
        }
        $span->start();
        $span->setKind(\Zipkin\Kind\SERVER);
        $span->setName($name);
        HttpUtils::saveHttpServerInfos($span);
        return $span;
    }

    /**
     * Creates a client span within an existing trace. 
     * @param Span $parent parent Span.
     * @param string $name child name of span.
     * @return Span
     */
    public function startClientSpan(Span $parent, string $name): Span
    {
        $tracer = $this->tracing->getTracer();
        $childSpan = $tracer->newChild($parent->getContext());
        $childSpan->start();
        $childSpan->setKind(\Zipkin\Kind\CLIENT);
        $childSpan->setName($name);
        return $childSpan;
    }

    /**
     * Creates a middleware span within an existing trace. It is a \Zipkin\Kind\CLIENT kind Span.
     * It tag component.type for Decorate Span.
     * @param Span $parent parent Span.
     * @param string $name child name of span.
     * @param Type $type Middleware type, details: https://github.com/megaease/easeagent-sdk-php/blob/main/doc/middleware-span.md.
     * @return Span
     */
    public function startMiddlewareSpan(Span $parent, string $name, Type $type): Span
    {
        $span = $this->startClientSpan($parent, $name);
        $span->tag(\Easeagent\Middleware\TAG, $type->value());
        return $span;
    }

    /**
     * Injects the Span Context into array and return
     * @param Span $span
     * @return array span id header.
     */
    public function injectorHeaders(Span $span): array
    {
        $headers = [];
        /* Injects the context into the wire */
        $injector = $this->tracing->getPropagation()->getInjector(new Map());
        $injector($span->getContext(), $headers);
        return $headers;
    }
}
