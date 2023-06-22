<?php

namespace Egamings\Prometheus\Http\Controllers;

use Egamings\Prometheus\Exporter\PrometheusExporter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Prometheus\RenderTextFormat;

class PrometheusMetricsController {
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var PrometheusExporter
     */
    protected $prometheusExporter;

    /**
     * @param ResponseFactory $responseFactory
     * @param PrometheusExporter $prometheusExporter
     */
    public function __construct(ResponseFactory $responseFactory, PrometheusExporter $prometheusExporter)
    {
        $this->responseFactory = $responseFactory;
        $this->prometheusExporter = $prometheusExporter;
    }

    /**
     * @return ResponseFactory
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * @return PrometheusExporter
     */
    public function getPrometheusExporter()
    {
        return $this->prometheusExporter;
    }

    /**
     * GET /metrics
     *
     * The route path is configurable in the prometheus.metrics_route_path config var, or the
     * PROMETHEUS_METRICS_ROUTE_PATH env var.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMetrics()
    {
        $metrics = $this->prometheusExporter->export();

        $renderer = new RenderTextFormat();
        $result = $renderer->render($metrics);

        return $this->responseFactory->make($result, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}

