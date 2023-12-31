<?php

namespace Egamings\Prometheus\Providers;

use Egamings\Prometheus\Console\PublishCommand;
use Egamings\Prometheus\Console\PublishConfigCommand;
use Egamings\Prometheus\Exporter\PrometheusExporter;
use Egamings\Prometheus\Factory\StorageAdapterFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Adapter;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
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
        $middleware = [];

        if (!empty(config('prometheus.metrics_route_middleware'))) {
            $middleware = [ 
                'middleware' => config('prometheus.metrics_route_middleware')
            ];
        }

        $this->app->router->group($middleware, function ($route) { /* @phpstan-ignore-line */
            $route->get(config('prometheus.metrics_route_path'), [ 
                'as' => config('prometheus.metrics_route_path'),
                'uses' => 'Egamings\Prometheus\Http\Controllers\PrometheusMetricsController@getMetrics',
            ]);
        });

        $exporter = $this->app->make(PrometheusExporter::class);

        foreach (config('prometheus.collectors') as $class) {
            $collector = $this->app->make($class);
            $exporter->registerCollector($collector);
        }
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {

        $configPath = __DIR__.'/../../config/prometheus.php';
        $this->mergeConfigFrom($configPath, 'prometheus');

        $this->app->singleton('command.prometheus.publish', function () {
            return new PublishCommand();
        });

        $this->app->singleton('command.prometheus.publish-config', function () {
            return new PublishConfigCommand();
        });

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

        $this->commands(
            'command.prometheus.publish',
            'command.prometheus.publish-config'
        );
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

