# Falcon Cache plugin for Craft CMS 3.x

This plugin helps you using cache proxies with Craft. 

It adds the neccessary `Cache-Control` and `XKEY/Surrogate-Key/Cache-Tag` headers to your pages. 
When Entries get updated it takes care of the cache invalidation. 

If you need an introduction to HTTP Caching, I highly recommend [this article](https://blog.fortrabbit.com/mastering-http-caching). 

## Supported Cache Drivers

* [KeyCDN](https://www.keycdn.com) (SaaS)
* [Fastly](https://www.fastly.com) (SaaS)
* Varnish (your own proxy)
* Dummy (does nothing, loggs purge actions)

## Installation

1. Install with Composer via `composer require ostark/falcon` from your project directory
2. Install plugin with this command `php craft install/plugin falcon` or in the Craft CP under Settings > Plugins

A new configuration file gets created in `your-project/config/falcon.php`. Most settings are controlled by ENV vars.



### Fastly Setup
```
FALCON_DRIVER=fastly
FASTLY_API_TOKEN=<REPLACE-ME>
FASTLY_SERVICE_ID=<REPLACE-ME>
```

### KeyCDN Setup
```
FALCON_DRIVER=keycdn
KEYCDN_API_KEY=<REPLACE-ME>
KEYCDN_ZONE_URL=<REPLACE-ME>.kxcdn.com
KEYCDN_ZONE_ID=<REPLACE-ME>
```

### Varnish Setup
```
FALCON_DRIVER=varnish
VARNISH_URL=<REPLACE-ME>
```

### Tuning

With `Cache-Control` headers you can disabled caching for certain templates:
```
{% header "Cache-Control: private, no-cache" %}
```

---

Craft 2 development happens here: https://github.com/joshangell/Falcon

---

### Performance results
![example](https://github.com/ostark/falcon-craft3/blob/master/resources/preformance.png)

### Cache Tag Headers
![example](https://github.com/ostark/falcon-craft3/blob/master/resources/response-header.png)


