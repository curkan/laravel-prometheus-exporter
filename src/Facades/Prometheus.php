<?php

namespace Egamings\Prometheus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class: Prometheus
 *
 * @see Facade
 */
class Prometheus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Egamings\Prometheus\Exporter\PrometheusExporter::class;
    }
}

