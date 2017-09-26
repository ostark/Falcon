<?php namespace ostark\falcon\events;

use yii\base\Event;


class CacheResponseEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var array Array of tags
     */
    public $tags = [];

    /**
     * @var string
     */
    public $requestUrl;

    /**
     * @var int Cache TTL in seconds
     */
    public $maxAge = 0;

    /**
     * @var string
     */
    public $output;

    /**
     * @var array Array of headers
     */
    public $headers = [];


}
