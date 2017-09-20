<?php namespace ostark\falcon\drivers;
use yii\base\Object;

/**
 * Created by PhpStorm.
 * User: os
 * Date: 13.09.17
 * Time: 12:54
 */
class AbstractPurger extends Object
{
    /**
     * @var string
     */
    protected $headerName;

    protected $queue = false;

    public function __construct($config)
    {
        // assign config to object properties
        parent::__construct($config);
    }


    public function getHttpClient() {

    }

    public function setheaderName($value) {
        $this->headerName = $value;
    }


}
