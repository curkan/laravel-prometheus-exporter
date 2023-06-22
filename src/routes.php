<?php

/** @var \Illuminate\Routing\Route $route */
$route = Route::get(
    config('prometheus.metrics_route_path'),
    \Egamings\Prometheus\Http\Controllers\PrometheusMetricsController::class . '@getMetrics'
);

if ($name = config('prometheus.metrics_route_name')) {
    $route->name($name);
}

$middleware = config('prometheus.metrics_route_middleware');

if ($middleware) {
    $route->middleware($middleware);
}
