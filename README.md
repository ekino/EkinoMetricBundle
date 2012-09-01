Ekino PHP Metric
================

[![Build Status](https://secure.travis-ci.org/ekino/EkinoMetricBundle.png?branch=master)](http://travis-ci.org/ekino/EkinoMetricBundle)

Integrate Ekino PHP Metric into Symfony2

**WARNING** - This bundle works with pending pull requests :

* https://github.com/schmittjoh/cg-library/pull/6
* https://github.com/schmittjoh/JMSAopBundle/pull/13

## Requirements

* This code must runs with [CollectD](http://collectd.org/) or [StatsD](https://github.com/etsy/statsd/) running
* Users must have a good understanding on collecting and aggregating data/solution

## Installation

### Using Composer

Use `composer.phar`:

```bash
$ php composer.phar require ekino/metric-bundle
```
You just have to specify the version you want : `dev-master`.
It will add the package in your `composer.json` file and install it.

Or you can do it by yourself, first, add the following to your `composer.json` file:

```js
// composer.json
{
    // ...
    require: {
        // ...
        "ekino/metric-bundle": "dev-master"
    }
}
```

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

```bash
$ php composer.phar update ekino/metric-bundle
```

### Configuration

```yaml
ekino_metric:
    metrics:
        - { type: timer, service: "event_dispatcher::dispatch",   name: "php.symfony.event_dispatcher.{arg0}" }
        - { type: timer, service: "http_kernel::handle",          name: "php.symfony.http_kernel.handle" }
        - { type: timer, service: "templating::render",           name: "php.symfony.twig.render.{arg0}" }
        - { type: timer, service: "mailer::send",                 name: "php.mailer.send" }
        - { type: timer, service: "router::match",                name: "php.symfony.router.match"}
        - { type: timer, service: "router::generate",             name: "php.symfony.router.generate.{arg0}"}

    reporter: ekino.metric.reporter.statsd # ekino.metric.reporter.collectd

    reporters:
        collectd:
            hostname:   web1-php    # the hostname to send to collectd
            udp_host:   localhost   # the host where the UDP stream need to be send
            udp_port:   25826       # the port where the UDP stream need to be send

        statsd:
            udp_host:   localhost
            udp_port:   8125

        newrelic:
            application_name: Awesome Aplication # (mandatory, default value in newrelic is PHP Application)
            api_key:                             # New Relic API
```

#### Doctrine Collector

If you want to collect information about Doctrine query, you need to enable the doctrine profiler.

```yaml
doctrine:
    dbal:
        default_connection: default
        connections:
            default:
                profiling: true
                driver:    %database_driver%
                dbname:    %database_name%
                user:      %database_user%
                host:      %database_host%
                password:  %database_password%

ekino_metric:
    [...]
    collectors: # configure built in collectors
        doctrine:
            prefix: 'mysql.query'

    collect_from:   # configure services to retrieve metric from
        - ekino.metric.collector.doctrine

```