<?php

namespace Egamings\Prometheus\MetricTypes;

use Prometheus\CollectorRegistry;

interface MetricTypeContract
{
    public function register(CollectorRegistry $registry): MetricTypeContract;
}

