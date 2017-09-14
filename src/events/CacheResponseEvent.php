<?php namespace joshangell\falcon\events;

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
    public $ttl = 0;


}
