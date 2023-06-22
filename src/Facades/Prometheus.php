<?php

namespace Egamings\Prometheus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Prometheus\Prometheus
 */
class Prometheus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Egamings\Prometheus\Prometheus::class;
    }
}

