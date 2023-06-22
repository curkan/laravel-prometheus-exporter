<?php

namespace Egamings\Prometheus;

use Egamings\Prometheus\Actions\RenderCollectorsAction;
use Egamings\Prometheus\MetricTypes\Gauge;
use Egamings\Prometheus\MetricTypes\MetricTypeContract;

class Prometheus
{
    /** @var array<\Spatie\Prometheus\MetricTypes\MetricType> */
    protected array $collectors = [];


    /**
     * addGauge
     *
     * @param string $label
     * @param null|float|callable $value
     * @param ?string $name
     * @param ?string $namespace
     * @param ?string $helpText
     *
     * @return Gauge 
     */
    public function addGauge(
        string $label,
        null|float|callable $value = null,
        ?string $name = null,
        ?string $namespace = null,
        ?string $helpText = null,
    ): Gauge {
        $collector = new Gauge($label, $value, $name, $namespace, $helpText);

        $this->registerCollector($collector);

        return $collector;
    }

    /**
     * addGauge
     *
     * @param string $label
     * @param null|float|callable $value
     * @param ?string $name
     * @param ?string $namespace
     * @param ?string $helpText
     *
     * @return Gauge 
     */
    public function addCounter(
        string $label,
        null|float|callable $value = null,
        ?string $name = null,
        ?string $namespace = null,
        ?string $helpText = null,
    ): Gauge {
        $collector = new Gauge($label, $value, $name, $namespace, $helpText);

        $this->registerCollector($collector);

        return $collector;
    }

    /**
     * registerCollector
     *
     * @param MetricTypeContract $collector
     *
     * @return self 
     */
    public function registerCollector(MetricTypeContract $collector): self
    {
        $this->collectors[] = $collector;

        return $this;
    }


    /**
     * renderCollectors
     *
     * @return string 
     */
    public function renderCollectors(): string
    {
        $collect = collect($this->collectors)->toArray();

        return app(RenderCollectorsAction::class)->execute($collect);
    }

}

