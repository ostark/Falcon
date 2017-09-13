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
    'driver'      => getenv('FALCON_DRIVER'),

    // TTL?
    'default_ttl' => 3600,

    // Exclude routes?
    'excludes'    => [],

    // Include routes?
    'includes'    => [],


    // Drivers settings
    'drivers'     => [

        'varnish' => [
            'url'        => getenv('VARNISH_URL'),
            'headerName' => 'X-HashTwo',
            'headers'    => [],
        ],

        'fastly' => [
            'apiKey'     => getenv('FASTLY_API_KEY'),
            'serviceId'  => getenv('FASTLY_SERVICE_ID'),
            'headerName' => 'Surrogate-Key',

        ],

        'keycdn' => [
            'apiKey'     => getenv('KEYCDN_API_KEY'),
            'zoneId'     => getenv('KEYCDN_ZONE_ID'),
            'domain'     => getenv('KEYCDN_DOMAIN'),
            'headerName' => 'Cache-Tag'
        ],

        'custom' => [
            'class'      => 'namespace\to\driver\Custom',
            'headerName' => 'X-Otic-Cache-Tag',
            'param1'     => '...',
            'param2'     => '...',
        ]
    ]
];
