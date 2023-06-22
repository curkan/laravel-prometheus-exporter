<?php

return [
    'enabled' => true,


    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace to use as a prefix for all metrics.
    |
    | This will typically be the name of your project, eg: 'search'.
    |
    */

    'namespace' => env('PROMETHEUS_NAMESPACE', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Route Path
    |--------------------------------------------------------------------------
    |
    | The path at which prometheus metrics are exported.
    |
    | This is only applicable if metrics_route_enabled is set to true.
    |
    */

    'metrics_route_path' => env('PROMETHEUS_METRICS_ROUTE_PATH', 'metrics'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Route Name
    |--------------------------------------------------------------------------
    |
    | Route Parh name aliase.
    |
    | This is only applicable if metrics_route_enabled is set to true.
    |
    */

    'metrics_route_name' => env('PROMETHEUS_METRICS_ROUTE_NAME', 'metrics'),

    /*
     * You can override these classes to customize low-level behaviour of the package.
     * In most cases, you can just use the defaults.
     */
    'actions' => [
        'render_collectors' => Spatie\Prometheus\Actions\RenderCollectorsAction::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Adapter
    |--------------------------------------------------------------------------
    |
    | The storage adapter to use.
    |
    | Supported: "memory", "redis", "apc"
    |
    */

    'storage_adapter' => env('PROMETHEUS_STORAGE_ADAPTER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Storage Adapters
    |--------------------------------------------------------------------------
    |
    | The storage adapter configs.
    |
    */

    'storage_adapters' => [

        'redis' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'port' => env('REDIS_PORT', 6379),
            'timeout' => 0.1,
            'read_timeout' => 10,
            'persistent_connections' => false,
            'prefix' => env('PROMETHEUS_REDIS_PREFIX', 'PROMETHEUS_'),
        ],

    ],

];

