<?php

namespace Egamings\Prometheus\Http\Controllers;

use Egamings\Prometheus\Exporter\PrometheusExporter;
use Prometheus\RenderTextFormat;

class PrometheusMetricsController {

    /**
     * @var PrometheusExporter
     */
    protected $prometheusExporter;

    /**
     * @param PrometheusExporter $prometheusExporter
     */
    public function __construct(PrometheusExporter $prometheusExporter)
    {
        $this->prometheusExporter = $prometheusExporter;
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

        return response($result, 201)->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}

