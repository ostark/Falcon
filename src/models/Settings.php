<?php

namespace ostark\falcon\models;

use Craft;
use craft\base\Model;

/**
 * Falcon Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Oliver Stark
 * @package   Falcon
 * @since     0.1.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $driver;


    /**
     * Some field model attribute
     *
     * @var array
     */
    public $drivers;

    /**
     * Some field model attribute
     *
     * @var int
     */
    public $defaultMaxAge = null;


    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['driver', 'drivers', 'localTagMap'], 'required'],
            // ...
        ];
    }

    /**
     * @return string
     */
    public function getHeaderName()
    {
        return $this->drivers[$this->driver]['headerName'];
    }

    /**
     * @return string
     */
    public function getHeaderTagDelimiter()
    {
        return $this->drivers[$this->driver]['tagHeaderDelimiter'] ?? ' ';
    }

    /**
     * @return array
     */
    public function getNoCacheElements()
    {
        return ['craft\elements\User', 'craft\elements\MatrixBlock'];
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function isCachableElement(string $class)
    {
        return in_array($class, $this->getNoCacheElements()) ? false : true;
    }

}
