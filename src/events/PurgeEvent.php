<?php namespace ostark\falcon\events;

use yii\base\Event;


class PurgeEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var array Array of tags
     */
    public $tags = [];


}
