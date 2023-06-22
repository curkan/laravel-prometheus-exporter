<?php

namespace Egamings\Prometheus\MetricTypes;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Prometheus\CollectorRegistry;
use Prometheus\Counter as PrometheusCounter;

class Counter implements MetricTypeContract
{
    protected array $values = [];

    public function __construct(
        protected string $label,
        null|float|Closure|array $value,
        protected ?string $name = null,
        protected ?string $namespace = null,
        protected ?string $helpText = null,
        protected ?array $labelNames = [],
    ) {
        $this->name = $name ?? Str::slug($this->label, '_');

        if (! is_null($value)) {
            $this->value($value);
        }

        $this->namespace = Str::of(config('prometheus.default_namespace'))
            ->slug('_')
            ->lower();
    }

    public function namespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function label(string $label): self
    {
        $this->labelNames[] = $label;

        return $this;
    }

    public function labels(array $labelNames): self
    {
        $this->labelNames = $labelNames;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function helpText(string $helpText): self
    {
        $this->helpText = $helpText;

        return $this;
    }

    public function value(array|float|Closure $value, array|string $labelValues = []): self
    {
        $labelValues = Arr::wrap($labelValues);

        $this->values[] = [$value, $labelValues];

        return $this;
    }

    public function register(CollectorRegistry $registry): self
    {
        $counter = $registry->getOrRegisterCounter(
            $this->namespace,
            $this->name,
            $this->helpText ?? '',
            $this->labelNames,
        );

        collect($this->values)
            ->each(function (array $valueAndLabels) use ($counter) {
                $this->handleValueAndLabels($counter, $valueAndLabels);
            });

        return $this;
    }

}

