# Falcon Cache plugin for Craft CMS 3.x

Craft 2 development happens here: https://github.com/joshangell/Falcon

## Features

- autotagging (surrogate keys / cache tags)
- multi cache proxy support (Varnish, Keycdn, Fastly, Cloudflare, Local)
- autoinvalidation

<<<<<<< HEAD
## Installation

1. Install with Composer via `composer require ostark/falcon` from your project directory
2. Install plugin with this command `php ./craft install/plugin falcon` or in the Craft Control Panel under Settings > Plugins

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

---

=======
>>>>>>> 0cd4858... Peparing 1.0 release
### Performance results
![example](https://github.com/ostark/falcon-craft3/blob/master/examples/preformance.png)

### Cache Tag Headers
![example](https://github.com/ostark/falcon-craft3/blob/master/examples/response-header.png)


