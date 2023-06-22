<?php

namespace Egamings\Prometheus\Providers;

use Egamings\Prometheus\Exporter\PrometheusExporter;
use Egamings\Prometheus\Factory\StorageAdapterFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Adapter;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/prometheus.php' => config_path('prometheus.php'),
        ]);

        $exporter = $this->app->make(PrometheusExporter::class); /* @var PrometheusExporter $exporter */
        foreach (config('prometheus.collectors') as $class) {
            $collector = $this->app->make($class);
            $exporter->registerCollector($collector);
        }

        $this->configureRoutes();
    }
    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    private function configureRoutes(): void
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = Route::get(
            config('prometheus.metrics_route_path'),
            \Superbalist\LaravelPrometheusExporter\MetricsController::class . '@getMetrics'
        );

        if ($name = config('prometheus.metrics_route_name')) {
            $route->name($name);
        }

        $middleware = config('prometheus.metrics_route_middleware');

        if ($middleware) {
            $route->middleware($middleware);
        }
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/prometheus.php', 'prometheus');

        $this->app->singleton(PrometheusExporter::class, function ($app) {
            $adapter = $app['prometheus.storage_adapter'];
            $prometheus = new CollectorRegistry($adapter);
            return new PrometheusExporter(config('prometheus.namespace'), $prometheus);
        });
        $this->app->alias(PrometheusExporter::class, 'prometheus');

        $this->app->bind('prometheus.storage_adapter_factory', function () {
            return new StorageAdapterFactory();
        });

        $this->app->bind(Adapter::class, function ($app) {
            $factory = $app['prometheus.storage_adapter_factory']; /** @var StorageAdapterFactory $factory */
            $driver = config('prometheus.storage_adapter');
            $configs = config('prometheus.storage_adapters');
            $config = Arr::get($configs, $driver, []);
            return $factory->make($driver, $config);
        });
        $this->app->alias(Adapter::class, 'prometheus.storage_adapter');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'prometheus',
            'prometheus.storage_adapter_factory',
            'prometheus.storage_adapter',
        ];
    }
}

