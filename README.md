# laravel-prometheus-exporter

A Prometheus Exporter for Laravel\Lumen.

Данный пакет - обертка над [jimdo/prometheus_client_php](https://github.com/Jimdo/prometheus_client_php) для Ларавель.


## Установка

```bash
composer require egamings/laravel-prometheus-exporter
```

**Для Laravel:**

```bash
php artisan prometheus:publish
```

**Для Lumen:**

```bash
php artisan prometheus:publish
```
Настройки в app.php
``
Register Service Providers
``
```php
$app->register(Egamings\Prometheus\Providers\PrometheusServiceProvider::class);
```
``
Register Config Files
``
```php
$app->configure('prometheus');
```


## Configuration

Пакет имеет конфигурацию по умолчанию, в которой используются следующие переменные среды.
```
PROMETHEUS_NAMESPACE=games

PROMETHEUS_METRICS_ROUTE_ENABLED=true
PROMETHEUS_METRICS_ROUTE_PATH=metrics
PROMETHEUS_METRICS_ROUTE_MIDDLEWARE=null

PROMETHEUS_STORAGE_ADAPTER=redis

REDIS_HOST=localhost
REDIS_PORT=6379
PROMETHEUS_REDIS_PREFIX=PROMETHEUS_
PROMETHEUS_REDIS_PASSWORD=DEV_SECRET_API_REDIS_PASSWORD
```

Чтобы настроить файл конфигурации, опубликуйте конфигурацию пакета с помощью Artisan. (Было в изначальной настройке)

Файл конфига находится в `app/config/prometheus.php`.


### Exporting Metrics

Пакет добавляет конечную точку `/metrics`, включенную по умолчанию, которая предоставляет все метрики, собранные сборщиками.

Это можно включить/выключить с помощью переменной `PROMETHEUS_METRICS_ROUTE_ENABLED`, а также изменить с помощью
`PROMETHEUS_METRICS_ROUTE_PATH` вар.

Если вы хотите защитить эту конечную точку, вы можете написать любой middlewate  и включить его с помощью
`PROMETHEUS_METRICS_ROUTE_MIDDLEWARE`.


## Использование

```php
$exporter = app('prometheus');
// или
$exporter = Prometheus::getFacadeRoot();
// или обращаяся к фасаду использовать методы
Prometheus::registerCounter()

// Вывод всех метрик
var_dump($exporter->export());

// следующие методы можно использовать для непосредственного создания и взаимодействия со счетчиками, датчиками и гистограммами
// эти методы обычно вызываются сборщиками, но их можно использовать для непосредственной регистрации любых пользовательских метрик,


// Создание счетчика
$counter = registerCounter('search_requests_total', 'The total number of search requests.');
$counter->inc(); // инкремент на 1
$counter->incBy(2);  // инкремент на другое значение

// Создание счетчика с дополнительными полями
$counter = Prometheus::registerCounter('search_requests_total', 'The total number of search requests.', ['request_type']);
$counter->inc(['GET']); // инкремент на 1
$counter->incBy(2, ['GET']); 

// Получение счетчика
$counter = Prometheus::getCounter('search_requests_total');

// Создание gauge
$gauge = Prometheus::registerGauge('users_online_total', 'The total number of users online.');
$gauge->inc(); // инкремент на 1
$gauge->incBy(2);
$gauge->dec(); // декремент на 1
$gauge->decBy(2);
$gauge->set(36); // установка на 36

// Создание gauge c дополнительными полями)
$gauge = Prometheus::registerGauge('users_online_total', 'The total number of users online.', ['group']);
$gauge->inc(['staff']); // инкремент на 1
$gauge->incBy(2, ['staff']);
$gauge->dec(['staff']);
$gauge->decBy(2, ['staff']);
$gauge->set(36, ['staff']);

// Получение gauge
$counter = Prometheus::getGauge('users_online_total');

// Создание гистограммы
$histogram = Prometheus::registerHistogram(
    'response_time_seconds',
    'The response time of a request.',
    [],
    [0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0]
);
// бакеты должны быть в порядке возрастания
// если бакеты не указаны, будут использоваться бакеты по умолчанию 0.005, 0.01, 0.025, 0.05, 0.075, 0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0
$histogram->observe(5.0);

// Создание гистограм с дополнительными полями
$histogram = $exporter->registerHistogram(
    'response_time_seconds',
    'The response time of a request.',
    ['request_type'],
    [0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0]
);

$histogram->observe(5.0, ['GET']);

// Получение гистограммы
$counter = $exporter->getHistogram('response_time_seconds');
```
