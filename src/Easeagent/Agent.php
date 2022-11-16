<?php

declare(strict_types=1);

namespace Easeagent;

use Easeagent\Middleware\Type;
use Zipkin\Span;
use Zipkin\Tracing;
use Zipkin\Propagation\Map;
use Zipkin\Propagation\DefaultSamplingFlags;
use Exception;

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

    public function getTracing(): Tracing
    {
        return $this->tracing;
    }

    public function flush()
    {
        $this->tracing->getTracer()->flush();
    }

    public function serverTransaction(callable $callback)
    {
        $span = $this->startServerSpan($_SERVER['REQUEST_METHOD']);
        try {
            return $callback($span);
        } catch (Exception $e) {
            $span->setError($e);
            throw $e;
        } finally {
            $span->finish();
        }
    }

    public function startServerSpan(string $name): Span
    {
        /* Extracts the context from the HTTP headers */
        $extractor = $this->tracing->getPropagation()->getExtractor(new Map());
        $extractedContext = $extractor(getallheaders());

        $tracer = $this->tracing->getTracer();
        if ($extractedContext->isEmpty()) {
            $extractedContext = DefaultSamplingFlags::createAsSampled();
            $span = $tracer->newTrace($extractedContext);
        } else {
            $span = $tracer->joinSpan($extractedContext);
        }
        $span->start();
        $span->setKind(\Zipkin\Kind\SERVER);
        $span->setName($name);
        $span->tag(\Easeagent\Constant\HTTP_TAG_METHOD, strtoupper($_SERVER['REQUEST_METHOD']));
        $span->tag(\Easeagent\Constant\HTTP_TAG_PATH, $_SERVER['REQUEST_URI']);
        $span->tag(\Easeagent\Constant\HTTP_TAG_ATTRIBUTE_ROUTE, strtoupper($_SERVER['REQUEST_METHOD']) . " " . $_SERVER['REQUEST_URI']);
        $span->tag(\Easeagent\Constant\HTTP_TAG_CLIENT_ADDRESS, $_SERVER['REMOTE_ADDR'] . ":" . $_SERVER['REMOTE_PORT']);
        $span->tag(\Easeagent\Constant\HTTP_TAG_SCRIPT_FILENAME, $_SERVER['SCRIPT_FILENAME']);
        return $span;
    }

    public function startClientSpan(Span $parent, string $name): Span
    {
        $tracer = $this->tracing->getTracer();
        $childSpan = $tracer->newChild($parent->getContext());
        $childSpan->start();
        $childSpan->setKind(\Zipkin\Kind\CLIENT);
        $childSpan->setName($name);
        return $childSpan;
    }

    public function startMiddlewareSpan(Span $parent, string $name, Type $type): Span
    {
        $span = $this->startClientSpan($parent, $name);
        $span->tag(\Easeagent\Middleware\TAG, $type->value());
        return $span;
    }

    public function injectorHeaders(Span $span): array
    {
        $headers = [];
        /* Injects the context into the wire */
        $injector = $this->tracing->getPropagation()->getInjector(new Map());
        $injector($span->getContext(), $headers);
        return $headers;
    }
}
