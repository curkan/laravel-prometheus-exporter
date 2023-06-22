<?php

$route->get(config('prometheus.metrics_route_path'), [  /* @phpstan-ignore-line */
    'as' => 'metrics_route_path',
    'uses' => 'Egamings\Prometheus\Http\Controllers\PrometheusMetricsController@getMetrics',
]);


// if ($name = config('prometheus.metrics_route_name')) {
//     $route->name($name);
// }
//
// $middleware = config('prometheus.metrics_route_middleware');
//
// if ($middleware) {
//     $route->middleware($middleware);
// }
//
