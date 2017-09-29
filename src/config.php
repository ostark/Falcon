<?php
/**
 * Falcon config.php
 *
 * This file exists only as a template for the Falcon settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'falcon.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */


return [

    // Which driver?
    'driver'        => getenv('FALCON_DRIVER') ?? 'dummy',

    // Default for Cache-control s-maxage
    'defaultMaxAge' => 3600 * 24 * 7,

    // Drivers settings
    'drivers'       => [

        // Varnish config
        'varnish' => [
            'headerName' => 'X-HashTwo',
            'url'        => getenv('VARNISH_URL'),
            'headers'    => [],
        ],

        // Fastly config
        'fastly' => [
            'headerName' => 'Surrogate-Key',
            'serviceId'  => getenv('FASTLY_SERVICE_ID'),
            'apiToken'   => getenv('FASTLY_API_TOKEN'),
            'domain'     => getenv('FASTLY_DOMAIN'),
        ],

        // KeyCDN config
        'keycdn' => [
            'headerName' => 'Cache-Tag',
            'apiKey'     => getenv('KEYCDN_API_KEY'),
            'zoneId'     => getenv('KEYCDN_ZONE_ID'),
            'zoneUrl'    => getenv('KEYCDN_ZONE_URL')
        ],

        // Dummy driver (default)
        'dummy' => [
            'headerName'      => 'X-Dummy-Cache-Tag',
            'logPurgeActions' => true,
        ]
    ]
];
