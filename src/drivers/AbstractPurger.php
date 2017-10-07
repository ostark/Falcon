<?php namespace ostark\falcon\drivers;

use yii\base\Object;

/**
 * Created by PhpStorm.
 * User: os
 * Date: 13.09.17
 * Time: 12:54
 *
 * @property bool $localTagMap
 */
class AbstractPurger extends Object
{
    public function __construct($config)
    {
        // assign config to object properties
        parent::__construct($config);
    }
}
